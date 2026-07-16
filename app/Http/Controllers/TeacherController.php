<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\ActivityLog;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Tampilkan Dashboard Guru.
     */
    public function dashboard(): View
    {
        $teacher = Auth::user();

        // 1. Hitung Ucapan Selamat
        $hour = \Carbon\Carbon::now('Asia/Jakarta')->hour;
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat siang';
        } elseif ($hour >= 15 && $hour < 19) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }

        // 2. Ambil ID Mapel yang Diampu
        $mySubjectIds = Subject::where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->pluck('id')
            ->toArray();

        // 3. Log Aktivitas Siswa: Membaca Materi
        $materialActivities = DB::table('material_student')
            ->join('users', 'material_student.student_id', '=', 'users.id')
            ->join('materials', 'material_student.material_id', '=', 'materials.id')
            ->join('subjects', 'materials.subject_id', '=', 'subjects.id')
            ->join('classes', 'subjects.class_id', '=', 'classes.id')
            ->whereIn('materials.subject_id', $mySubjectIds)
            ->select('users.name as student_name', 'materials.title as material_title', 'classes.name as class_name', 'material_student.created_at')
            ->orderBy('material_student.created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Log Aktivitas Siswa: Mengumpulkan Tugas
        $submissionActivities = Submission::with(['student', 'assignment.subject.classroom'])
            ->whereHas('assignment', function($q) use ($mySubjectIds) {
                $q->whereIn('subject_id', $mySubjectIds);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 5. Pie Chart: Pengumpulan Tugas per Kelas
        $classroomIds = Subject::whereIn('id', $mySubjectIds)->pluck('class_id')->unique()->toArray();
        $classrooms = Classroom::whereIn('id', $classroomIds)->withCount('students')->get();

        $pieData = [];
        foreach ($classrooms as $cr) {
            $crAssignmentIds = Assignment::whereHas('subject', function($q) use ($cr, $mySubjectIds) {
                $q->where('class_id', $cr->id)->whereIn('id', $mySubjectIds);
            })->pluck('id')->toArray();

            $submissionsCount = Submission::whereIn('assignment_id', $crAssignmentIds)->count();
            $pieData[] = [
                'class_name' => $cr->name,
                'submissions_count' => $submissionsCount,
                'students_count' => $cr->students_count
            ];
        }

        // 6. Bar Chart: Rerata Nilai Tertinggi Kelas
        $barData = [];
        foreach ($classrooms as $cr) {
            $averageGrade = Grade::whereHas('submission', function($q) use ($cr, $mySubjectIds) {
                $q->whereHas('assignment', function($q2) use ($cr, $mySubjectIds) {
                    $q2->whereIn('subject_id', $mySubjectIds)
                       ->whereHas('subject', function($q3) use ($cr) {
                           $q3->where('class_id', $cr->id);
                       });
                });
            })->avg('score');

            $barData[] = [
                'class_name' => $cr->name,
                'average_grade' => round($averageGrade ?? 0, 1)
            ];
        }

        // 7. Top 10 Rankings
        $studentIds = DB::table('class_students')->whereIn('class_id', $classroomIds)->pluck('student_id')->unique()->toArray();
        $students = User::whereIn('id', $studentIds)->where('role', 'student')->get();

        $rankings = [];
        foreach ($students as $student) {
            $averageGrade = Grade::whereHas('submission', function($q) use ($student, $mySubjectIds) {
                $q->where('student_id', $student->id)
                  ->whereHas('assignment', function($q2) use ($mySubjectIds) {
                      $q2->whereIn('subject_id', $mySubjectIds);
                  });
            })->avg('score') ?? 0;

            $myMaterialsCount = Material::whereIn('subject_id', $mySubjectIds)->count();
            $myAssignmentsCount = Assignment::whereIn('subject_id', $mySubjectIds)->count();
            $totalItems = $myMaterialsCount + $myAssignmentsCount;

            $completedMaterials = $student->completedMaterials()->whereIn('material_id', Material::whereIn('subject_id', $mySubjectIds)->pluck('id'))->count();
            $completedAssignments = Submission::where('student_id', $student->id)
                ->whereHas('assignment', function($q) use ($mySubjectIds) {
                    $q->whereIn('subject_id', $mySubjectIds);
                })->count();

            $completedItems = $completedMaterials + $completedAssignments;
            $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

            if ($progress > 0 || $averageGrade > 0) {
                $rankings[] = [
                    'name' => $student->name,
                    'class_name' => $student->classes()->first()->name ?? '-',
                    'average_grade' => round($averageGrade, 1),
                    'progress' => $progress
                ];
            }
        }

        usort($rankings, function($a, $b) {
            if ($b['average_grade'] == $a['average_grade']) {
                return $b['progress'] <=> $a['progress'];
            }
            return $b['average_grade'] <=> $a['average_grade'];
        });

        $rankings = array_slice($rankings, 0, 10);

        return view('teacher.dashboard', compact(
            'greeting',
            'materialActivities',
            'submissionActivities',
            'pieData',
            'barData',
            'rankings'
        ));
    }

    /**
     * Tampilkan Halaman Jadwal (Self vs All).
     */
    public function schedule(Request $request): View
    {
        $teacher = Auth::user();
        $filter = $request->get('filter', 'self'); // self | all

        $mySubjects = Subject::with(['classroom', 'teachers'])
            ->where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->get();

        $allClasses = Classroom::with(['subjects.teachers', 'subjects.classroom'])->orderBy('name')->get();

        return view('teacher.schedule', compact('mySubjects', 'allClasses', 'filter'));
    }

    /**
     * Monitoring Kelas Wali (Khusus Wali Kelas).
     */
    public function myClass(): View
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->with(['students', 'subjects'])->first();

        if (!$classroom) {
            abort(403, 'Anda bukan wali kelas dari kelas mana pun.');
        }

        $subjects = $classroom->subjects;
        $studentsData = [];

        foreach ($classroom->students as $student) {
            $subjGrades = [];
            foreach ($subjects as $subj) {
                $avgScore = Grade::whereHas('submission', function($q) use ($student, $subj) {
                    $q->where('student_id', $student->id)->whereHas('assignment', function($q2) use ($subj) {
                        $q2->where('subject_id', $subj->id);
                    });
                })->avg('score');

                $subjGrades[$subj->id] = $avgScore ? round($avgScore, 1) : '-';
            }

            // Hitung Progres Belajar di kelas wali
            $totalMaterials = Material::whereIn('subject_id', $subjects->pluck('id'))->count();
            $totalAssignments = Assignment::whereIn('subject_id', $subjects->pluck('id'))->count();
            $totalItems = $totalMaterials + $totalAssignments;

            $completedMat = $student->completedMaterials()->whereIn('material_id', Material::whereIn('subject_id', $subjects->pluck('id'))->pluck('id'))->count();
            $completedAss = Submission::where('student_id', $student->id)
                ->whereIn('assignment_id', Assignment::whereIn('subject_id', $subjects->pluck('id'))->pluck('id'))->count();

            $progress = $totalItems > 0 ? round((($completedMat + $completedAss) / $totalItems) * 100) : 0;

            $studentsData[] = [
                'name' => $student->name,
                'email' => $student->email,
                'grades' => $subjGrades,
                'progress' => $progress
            ];
        }

        return view('teacher.my-class', compact('classroom', 'subjects', 'studentsData'));
    }

    /**
     * Tampilkan Halaman Input Nilai Mapel.
     */
    public function gradesIndex(Request $request): View
    {
        $teacher = Auth::user();
        
        // Ambil mapel yang diampu beserta tugas di dalamnya
        $mySubjects = Subject::with(['classroom', 'assignments'])
            ->where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->get();

        $selectedAssignmentId = $request->get('assignment_id');
        $students = collect();
        $submissions = collect();
        $selectedAssignment = null;

        if ($selectedAssignmentId) {
            $selectedAssignment = Assignment::with('subject.classroom')->findOrFail($selectedAssignmentId);
            
            // Dapatkan seluruh siswa terdaftar di kelas mapel tersebut
            $students = User::whereHas('classes', function($q) use ($selectedAssignment) {
                $q->where('classes.id', $selectedAssignment->subject->class_id);
            })->where('role', 'student')->orderBy('name')->get();

            // Dapatkan submission yang ada untuk tugas ini
            $submissions = Submission::where('assignment_id', $selectedAssignmentId)
                ->with('grade')
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.grades', compact('mySubjects', 'selectedAssignmentId', 'selectedAssignment', 'students', 'submissions'));
    }

    /**
     * Simpan / Perbarui nilai siswa.
     */
    public function gradesStore(Request $request): RedirectResponse
    {
        $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'grades' => ['required', 'array'],
        ]);

        $assignmentId = $request->assignment_id;

        foreach ($request->grades as $studentId => $data) {
            $score = $data['score'] ?? null;
            $feedback = $data['feedback'] ?? null;

            // Jangan buat data nilai jika score kosong
            if ($score === null || $score === '') {
                continue;
            }

            // Buat dummy submission jika belum ada pengumpulan fisik
            $submission = Submission::firstOrCreate([
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
            ], [
                'content' => 'Input nilai langsung oleh guru (tanpa pengumpulan file/isi)',
                'status' => 'graded'
            ]);

            // Update status submission
            $submission->update(['status' => 'graded']);

            // Simpan atau update Grade
            Grade::updateOrCreate([
                'submission_id' => $submission->id,
            ], [
                'score' => floatval($score),
                'feedback' => $feedback,
                'graded_by' => Auth::id(),
            ]);
        }

        // Catat Log Aktivitas
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Menginput / memperbarui nilai massal untuk tugas ID {$assignmentId}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Nilai siswa berhasil disimpan.');
    }

    /**
     * Tampilkan Halaman Pengumuman Guru.
     */
    public function announcementsIndex(): View
    {
        $teacher = Auth::user();

        // Ambil kelas tempat guru mengajar
        $classroomIds = Subject::where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->pluck('class_id')
            ->unique()
            ->toArray();

        $classrooms = Classroom::whereIn('id', $classroomIds)->orderBy('name')->get();

        $announcements = Announcement::with('classroom')
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.announcements', compact('classrooms', 'announcements'));
    }

    /**
     * Simpan Pengumuman Baru.
     */
    public function announcementsStore(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'], // Max 5MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create([
            'class_id' => $request->class_id,
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
        ]);

        // Catat Log Aktivitas
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat pengumuman baru: {$announcement->title} untuk kelas ID {$announcement->class_id}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil dipublikasikan kepada siswa.');
    }

    /**
     * Tampilkan halaman kelola materi.
     */
    public function materialsIndex(): View
    {
        $teacher = Auth::user();

        // Ambil mapel yang diampu beserta materi
        $mySubjects = Subject::with(['classroom', 'materials'])
            ->where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->get();

        return view('teacher.materials', compact('mySubjects'));
    }

    /**
     * Simpan materi baru.
     */
    public function materialsStore(Request $request): RedirectResponse
    {
        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg', 'max:10240'], // Max 10MB
            'video_url' => ['nullable', 'url', 'max:255'],
        ]);

        $teacher = Auth::user();
        $subject = Subject::where('id', $request->subject_id)
            ->where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('teachers', function($q) use ($teacher) {
                          $q->where('users.id', $teacher->id);
                      });
            })->firstOrFail();

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
        }

        $material = Material::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
            'video_url' => $request->video_url,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Mengunggah materi baru: {$material->title} untuk mapel {$subject->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Materi belajar berhasil ditambahkan.');
    }

    /**
     * Tampilkan halaman kelola tugas.
     */
    public function assignmentsIndex(): View
    {
        $teacher = Auth::user();

        // Ambil mapel yang diampu beserta tugas-tugasnya
        $mySubjects = Subject::with(['classroom', 'assignments'])
            ->where('teacher_id', $teacher->id)
            ->orWhereHas('teachers', function($q) use ($teacher) {
                $q->where('users.id', $teacher->id);
            })
            ->get();

        return view('teacher.assignments', compact('mySubjects'));
    }

    /**
     * Simpan tugas baru.
     */
    public function assignmentsStore(Request $request): RedirectResponse
    {
        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:essay,file,quiz,coding,project'],
            'deadline' => ['required', 'date_format:Y-m-d\TH:i'],
            'file' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg', 'max:10240'], // Max 10MB
        ]);

        $teacher = Auth::user();
        $subject = Subject::where('id', $request->subject_id)
            ->where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('teachers', function($q) use ($teacher) {
                          $q->where('users.id', $teacher->id);
                      });
            })->firstOrFail();

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        $assignment = Assignment::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'deadline' => \Carbon\Carbon::parse($request->deadline),
            'file_path' => $filePath,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat tugas baru: {$assignment->title} untuk mapel {$subject->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Tugas baru berhasil dipublikasikan.');
    }

    /**
     * API detail jawaban siswa per tugas.
     */
    public function getAssignmentSubmissions($id)
    {
        $teacher = Auth::user();
        $assignment = Assignment::with('subject')->findOrFail($id);

        // Verifikasi kepemilikan
        $subject = Subject::where('id', $assignment->subject_id)
            ->where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('teachers', function($q) use ($teacher) {
                          $q->where('users.id', $teacher->id);
                      });
            })->firstOrFail();

        // Ambil semua siswa di kelas tugas tersebut
        $students = User::whereHas('classes', function($q) use ($subject) {
            $q->where('classes.id', $subject->class_id);
        })->where('role', 'student')->orderBy('name')->get();

        $submissions = Submission::where('assignment_id', $id)
            ->with('grade')
            ->get()
            ->keyBy('student_id');

        $data = [];
        foreach ($students as $student) {
            $sub = $submissions->get($student->id);
            $data[] = [
                'student_name' => $student->name,
                'student_email' => $student->email,
                'status' => $sub && $sub->status !== 'draft' ? 'submitted' : 'none',
                'submitted_at' => $sub && $sub->status !== 'draft' ? $sub->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : null,
                'content' => $sub && $sub->status !== 'draft' ? $sub->content : null,
                'file_url' => $sub && $sub->status !== 'draft' && $sub->file_path ? asset('storage/' . $sub->file_path) : null,
                'score' => $sub && $sub->status !== 'draft' && $sub->grade ? intval($sub->grade->score) : null,
                'feedback' => $sub && $sub->status !== 'draft' && $sub->grade ? $sub->grade->feedback : null,
            ];
        }

        return response()->json($data);
    }
}

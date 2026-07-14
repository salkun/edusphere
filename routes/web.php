<?php

use App\Http\Controllers\ProfileController;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Material;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user && $user->role === 'admin') {
        $logs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard-admin', compact('logs'));
    }

    // Menghitung ucapan selamat berdasarkan waktu Purwakarta (WIB / Asia/Jakarta)
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
    
    // Mengambil kelas pertama tempat siswa terdaftar
    $classroom = $user->classes()->first();
    
    $subjects = collect();
    $upcomingAssignments = collect();
    $recentMaterials = collect();
    $completedMaterialIds = [];
    $submittedAssignmentIds = [];
    $totalClassItemsCount = 0;
    $completedClassItemsCount = 0;
    $globalProgress = 0;
    $streak = 0;
    
    if ($classroom) {
        // Ambil ID materi yang sudah diselesaikan oleh siswa ini
        $completedMaterialIds = $user->completedMaterials()->pluck('material_id')->toArray();
        
        // Ambil ID tugas yang sudah dikumpulkan oleh siswa ini
        $submittedAssignmentIds = \DB::table('submissions')
            ->where('student_id', $user->id)
            ->pluck('assignment_id')
            ->toArray();

        // Mengambil semua mata pelajaran di kelas tersebut beserta gurunya, materi, dan tugasnya
        $subjects = Subject::with(['teacher', 'materials', 'assignments'])
            ->where('class_id', $classroom->id)
            ->get();

        // Hitung progres belajar per mata pelajaran (materi + tugas)
        foreach ($subjects as $subject) {
            $totalMaterials = $subject->materials->count();
            $totalAssignments = $subject->assignments->count();
            $totalItems = $totalMaterials + $totalAssignments;
            
            $completedMaterials = $subject->materials->whereIn('id', $completedMaterialIds)->count();
            $completedAssignments = $subject->assignments->whereIn('id', $submittedAssignmentIds)->count();
            $completedItems = $completedMaterials + $completedAssignments;
            
            // Set property dinamis untuk view
            $subject->total_items = $totalItems;
            $subject->completed_items = $completedItems;
            $subject->progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
            
            // Akumulasi progres global
            $totalClassItemsCount += $totalItems;
            $completedClassItemsCount += $completedItems;
        }
            
        $subjectIds = $subjects->pluck('id');
        
        // Mengambil tugas mendatang (belum melewati deadline)
        $upcomingAssignments = Assignment::with('subject.teacher')
            ->whereIn('subject_id', $subjectIds)
            ->where('deadline', '>', now())
            ->orderBy('deadline', 'asc')
            ->get();
            
        // Mengambil materi terbaru
        $recentMaterials = Material::with('subject.teacher')
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung progres global
        $globalProgress = $totalClassItemsCount > 0 
            ? round(($completedClassItemsCount / $totalClassItemsCount) * 100) 
            : 0;

        // Hitung streak belajar (berdasarkan aktivitas penyelesaian materi + pengumpulan tugas)
        $materialDates = $user->completedMaterials()
            ->latest('material_student.created_at')
            ->get()
            ->map(fn($m) => \Carbon\Carbon::parse($m->pivot->created_at)->setTimezone('Asia/Jakarta')->startOfDay());

        $submissionDates = \DB::table('submissions')
            ->where('student_id', $user->id)
            ->latest('created_at')
            ->get()
            ->map(fn($s) => \Carbon\Carbon::parse($s->created_at)->setTimezone('Asia/Jakarta')->startOfDay());

        $completedDates = $materialDates->concat($submissionDates)
            ->unique(fn($date) => $date->toDateString())
            ->values()
            ->sort(fn($a, $b) => $b->timestamp <=> $a->timestamp)
            ->values();

        $streak = 0;
        if ($completedDates->isNotEmpty()) {
            $today = \Carbon\Carbon::today('Asia/Jakarta');
            $yesterday = \Carbon\Carbon::yesterday('Asia/Jakarta');
            $latestActivityDate = $completedDates->first();
            
            if ($latestActivityDate->eq($today) || $latestActivityDate->eq($yesterday)) {
                $streak = 1;
                $currentDate = $latestActivityDate;
                
                for ($i = 1; $i < $completedDates->count(); $i++) {
                    $prevDate = $completedDates->get($i);
                    $diff = $currentDate->diffInDays($prevDate);
                    
                    if ($diff == 1) {
                        $streak++;
                        $currentDate = $prevDate;
                    } elseif ($diff > 1) {
                        break;
                    }
                }
            }
        }
    }
    
    return view('dashboard', compact(
        'classroom', 
        'subjects', 
        'upcomingAssignments', 
        'recentMaterials',
        'completedMaterialIds',
        'submittedAssignmentIds',
        'totalClassItemsCount',
        'completedClassItemsCount',
        'globalProgress',
        'greeting',
        'streak'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/materials/{material}/toggle', function (\App\Models\Material $material) {
    $user = auth()->user();
    if ($user->completedMaterials()->where('material_id', $material->id)->exists()) {
        $user->completedMaterials()->detach($material->id);
    } else {
        $user->completedMaterials()->attach($material->id);
    }
    return back();
})->middleware('auth')->name('materials.toggle');

Route::get('/my-class', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas dan mata pelajaran terjadwal
    $classroom = $user->classes()->with(['homeroomTeacher', 'subjects.teacher'])->first();
    
    $subjects = collect();
    if ($classroom) {
        // Urutkan mata pelajaran berdasarkan hari dan jam mulai
        $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $subjects = $classroom->subjects->sortBy(function ($subject) use ($dayOrder) {
            return [$dayOrder[$subject->day] ?? 9, $subject->start_time];
        });
    }
    
    return view('my-class', compact('classroom', 'subjects'));
})->middleware(['auth', 'verified', 'menu.visible:class'])->name('my-class');

Route::get('/materials', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas dan mata pelajaran serta materinya
    $classroom = $user->classes()->with(['homeroomTeacher', 'subjects.teacher', 'subjects.materials'])->first();
    
    $subjects = collect();
    $completedMaterialIds = [];
    if ($classroom) {
        $subjects = $classroom->subjects;
        $completedMaterialIds = $user->completedMaterials()->pluck('material_id')->toArray();
    }
    
    return view('materials', compact('classroom', 'subjects', 'completedMaterialIds'));
})->middleware(['auth', 'verified', 'menu.visible:materials'])->name('materials');

Route::get('/assignments', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas
    $classroom = $user->classes()->with(['homeroomTeacher'])->first();
    
    $assignments = collect();
    $submittedAssignmentIds = [];
    if ($classroom) {
        $subjectIds = \App\Models\Subject::where('class_id', $classroom->id)->pluck('id');
        
        // Ambil semua tugas untuk kelas ini
        $assignments = \App\Models\Assignment::with(['subject.teacher'])
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('deadline', 'asc')
            ->get();
            
        // Ambil ID tugas yang sudah dikumpulkan oleh siswa ini
        $submittedAssignmentIds = \DB::table('submissions')
            ->where('student_id', $user->id)
            ->pluck('assignment_id')
            ->toArray();
    }
    
    return view('assignments', compact('classroom', 'assignments', 'submittedAssignmentIds'));
})->middleware(['auth', 'verified', 'menu.visible:assignments'])->name('assignments');

Route::get('/assignments/{assignment}', function (\App\Models\Assignment $assignment) {
    $user = auth()->user();
    
    // Pastikan siswa memiliki akses ke tugas ini (berada di kelas yang sama)
    $classroom = $user->classes()->first();
    if (!$classroom) {
        abort(403);
    }
    
    $subjectIds = \App\Models\Subject::where('class_id', $classroom->id)->pluck('id')->toArray();
    if (!in_array($assignment->subject_id, $subjectIds)) {
        abort(403);
    }
    
    $assignment->load(['subject.teacher']);
    
    // Cek apakah sudah dikumpulkan
    $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
        ->where('student_id', $user->id)
        ->first();
        
    return view('assignments-show', compact('classroom', 'assignment', 'submission'));
})->middleware(['auth', 'verified', 'menu.visible:assignments'])->name('assignments.show');

Route::post('/assignments/{assignment}/submit', function (\App\Models\Assignment $assignment) {
    $user = auth()->user();
    
    // Pastikan siswa memiliki akses ke tugas ini
    $classroom = $user->classes()->first();
    if (!$classroom) {
        abort(403);
    }
    
    $subjectIds = \App\Models\Subject::where('class_id', $classroom->id)->pluck('id')->toArray();
    if (!in_array($assignment->subject_id, $subjectIds)) {
        abort(403);
    }
    
    // Validasi input
    request()->validate([
        'content' => 'nullable|string',
        'file' => 'nullable|file|mimes:pdf,zip,rar,png,jpg,jpeg|max:10240', // Max 10MB
    ]);
    
    $filePath = null;
    if (request()->hasFile('file')) {
        $filePath = request()->file('file')->store('submissions', 'public');
    }
    
    // Simpan atau update Submission
    \App\Models\Submission::updateOrCreate(
        [
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
        ],
        [
            'content' => request('content'),
            'file_path' => $filePath,
            'status' => 'submitted',
        ]
    );
    
    return redirect()->route('assignments')->with('success', 'Tugas berhasil dikumpulkan!');
})->middleware(['auth', 'verified', 'menu.visible:assignments'])->name('assignments.submit');

Route::get('/announcements', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas
    $classroom = $user->classes()->with(['homeroomTeacher'])->first();
    
    $announcements = collect();
    if ($classroom) {
        // Ambil seluruh pengumuman untuk kelas ini, diurutkan dari yang terbaru
        $announcements = \App\Models\Announcement::with(['teacher'])
            ->where('class_id', $classroom->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    return view('announcements', compact('classroom', 'announcements'));
})->middleware(['auth', 'verified', 'menu.visible:announcements'])->name('announcements');

Route::get('/grades', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas
    $classroom = $user->classes()->with(['homeroomTeacher'])->first();
    
    // Mengambil seluruh pilihan semester yang tersedia untuk siswa ini
    $availableSemesters = \App\Models\ReportCard::where('student_id', $user->id)
        ->pluck('semester')
        ->toArray();
        
    // Menentukan semester terpilih (default: semester pertama/terbaru jika tidak ada input)
    $selectedSemester = request('semester');
    if (!$selectedSemester || !in_array($selectedSemester, $availableSemesters)) {
        $selectedSemester = reset($availableSemesters) ?: null;
    }
    
    // Mengambil data rapor lengkap siswa ini berdasarkan semester yang dipilih
    $reportCard = null;
    if ($selectedSemester) {
        $reportCard = \App\Models\ReportCard::with(['items.subject.teacher'])
            ->where('student_id', $user->id)
            ->where('semester', $selectedSemester)
            ->first();
    }
        
    return view('grades', compact('classroom', 'reportCard', 'availableSemesters', 'selectedSemester'));
})->middleware(['auth', 'verified', 'menu.visible:grades'])->name('grades');

Route::get('/settings', function () {
    return view('settings');
})->middleware(['auth', 'verified'])->name('settings');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('index');
    Route::post('/settings/sidebar', [\App\Http\Controllers\AdminController::class, 'updateRoleSidebar'])->name('settings.sidebar');

    // User CRUD
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{user}/update', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/delete', [\App\Http\Controllers\AdminController::class, 'destroyUser'])->name('users.destroy');

    // Class CRUD
    Route::get('/classes', [\App\Http\Controllers\AdminController::class, 'classesIndex'])->name('classes.index');
    Route::post('/classes', [\App\Http\Controllers\AdminController::class, 'storeClass'])->name('classes.store');
    Route::post('/classes/{class}/update', [\App\Http\Controllers\AdminController::class, 'updateClass'])->name('classes.update');
    Route::post('/classes/{class}/delete', [\App\Http\Controllers\AdminController::class, 'destroyClass'])->name('classes.destroy');

    // Subject/Mapel CRUD & Schedule
    Route::get('/subjects', [\App\Http\Controllers\AdminController::class, 'subjectsIndex'])->name('subjects.index');
    Route::post('/subjects', [\App\Http\Controllers\AdminController::class, 'storeSubject'])->name('subjects.store');
    Route::post('/subjects/{subject}/update', [\App\Http\Controllers\AdminController::class, 'updateSubject'])->name('subjects.update');
    Route::post('/subjects/{subject}/delete', [\App\Http\Controllers\AdminController::class, 'destroySubject'])->name('subjects.destroy');
});

require __DIR__.'/auth.php';

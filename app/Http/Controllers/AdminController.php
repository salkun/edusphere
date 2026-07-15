<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman Konfigurasi Sidebar.
     */
    public function index(): View
    {
        return view('admin');
    }

    public function usersIndex(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->role, ['student', 'teacher', 'admin'])) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Tampilkan halaman Kelola Kelas.
     */
    public function classesIndex(): View
    {
        $classes = Classroom::with(['homeroomTeacher', 'subjects.teachers', 'students'])->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $masterSubjects = \App\Models\MasterSubject::with('teachers')->orderBy('name')->get();
        $allStudents = User::where('role', 'student')->orderBy('name')->get();
        
        return view('admin.classes', compact('classes', 'teachers', 'masterSubjects', 'allStudents'));
    }

    /**
     * Tampilkan halaman Kelola Mapel & Jadwal.
     */
    public function subjectsIndex(): View
    {
        $subjects = \App\Models\MasterSubject::with('teachers')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.subjects', compact('subjects', 'teachers'));
    }

    /**
     * Simpan pengaturan visibilitas menu sidebar per role.
     */
    public function updateRoleSidebar(Request $request): RedirectResponse
    {
        $roles = ['student', 'teacher', 'admin'];
        $menus = ['class', 'materials', 'assignments', 'announcements', 'grades'];

        foreach ($roles as $role) {
            foreach ($menus as $menu) {
                $key = $role . '_menu_' . $menu . '_visible';
                $value = $request->has($key) ? 'true' : 'false';
                SystemSetting::set($key, $value);
            }
        }

        // Catat aktivitas ke log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Memperbarui konfigurasi visibilitas menu sidebar per role',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Konfigurasi sidebar per role berhasil diperbarui.');
    }

    /**
     * CRUD: Simpan kelas baru.
     */
    public function storeClass(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:classes,name', 'max:255'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $classroom = Classroom::create([
            'name' => $request->name,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat kelas baru: {$classroom->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Kelas {$classroom->name} berhasil ditambahkan.");
    }

    /**
     * CRUD: Perbarui data kelas.
     */
    public function updateClass(Request $request, Classroom $class): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', "unique:classes,name,{$class->id}", 'max:255'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $oldName = $class->name;
        $class->update([
            'name' => $request->name,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui kelas {$oldName} menjadi {$class->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Kelas {$class->name} berhasil diperbarui.");
    }

    /**
     * CRUD: Hapus kelas.
     */
    public function destroyClass(Request $request, Classroom $class): RedirectResponse
    {
        $className = $class->name;
        $class->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Menghapus kelas {$className}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Kelas {$className} berhasil dihapus.");
    }

    /**
     * CRUD: Simpan mata pelajaran baru.
     */
    public function storeSubject(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:master_subjects,name'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:users,id'],
        ]);

        $subject = \App\Models\MasterSubject::create([
            'name' => $request->name,
        ]);

        if ($request->filled('teacher_ids')) {
            $subject->teachers()->sync($request->teacher_ids);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat master mata pelajaran baru: {$subject->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Master mata pelajaran {$subject->name} berhasil ditambahkan.");
    }

    /**
     * CRUD: Perbarui mata pelajaran.
     */
    public function updateSubject(Request $request, $id): RedirectResponse
    {
        $subject = \App\Models\MasterSubject::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:master_subjects,name,' . $subject->id],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:users,id'],
        ]);

        $oldName = $subject->name;
        $subject->update([
            'name' => $request->name,
        ]);

        $subject->teachers()->sync($request->teacher_ids ?? []);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui master mata pelajaran {$oldName} menjadi {$subject->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Master mata pelajaran {$subject->name} berhasil diperbarui.");
    }

    /**
     * CRUD: Hapus mata pelajaran.
     */
    public function destroySubject(Request $request, $id): RedirectResponse
    {
        $subject = \App\Models\MasterSubject::findOrFail($id);
        $subjectName = $subject->name;
        $subject->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Menghapus master mata pelajaran {$subjectName}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Master mata pelajaran {$subjectName} berhasil dihapus.");
    }

    /**
     * Menyimpan jadwal kelas secara massal (batch save).
     */
    public function saveClassSchedule(Request $request, $classId): RedirectResponse
    {
        $request->validate([
            'schedules' => ['required', 'string'],
            'deleted_ids' => ['nullable', 'string'],
        ]);

        $class = Classroom::findOrFail($classId);

        $schedules = json_decode($request->schedules, true) ?? [];
        $deletedIds = json_decode($request->deleted_ids, true) ?? [];

        // 1. Hapus jadwal yang ditandai untuk dihapus
        if (!empty($deletedIds)) {
            Subject::whereIn('id', $deletedIds)->where('class_id', $classId)->delete();
        }

        // 2. Tambah / Update Jadwal
        foreach ($schedules as $sched) {
            $startTimeStr = $sched['start_time'] ?? '07:00';
            $durationVal = intval($sched['duration'] ?? 90);
            if ($durationVal < 1) {
                $durationVal = 90;
            }

            // Hitung jam selesai
            $startTime = \Carbon\Carbon::parse($startTimeStr);
            $endTimeStr = $startTime->copy()->addMinutes($durationVal)->format('H:i');

            // Format day(s)
            $dayValue = '';
            if (isset($sched['days']) && is_array($sched['days'])) {
                $dayValue = implode(',', $sched['days']);
            } elseif (isset($sched['day'])) {
                $dayValue = $sched['day'];
            }

            if (empty($dayValue)) {
                $dayValue = 'Senin';
            }

            // Cek apakah item baru atau lama
            if (isset($sched['is_new']) && $sched['is_new']) {
                $subject = Subject::create([
                    'name' => $sched['name'],
                    'class_id' => $classId,
                    'day' => $dayValue,
                    'start_time' => $startTimeStr,
                    'end_time' => $endTimeStr,
                ]);
            } else {
                $subject = Subject::where('id', $sched['id'])->where('class_id', $classId)->first();
                if ($subject) {
                    $subject->update([
                        'day' => $dayValue,
                        'start_time' => $startTimeStr,
                        'end_time' => $endTimeStr,
                    ]);
                }
            }

            if ($subject) {
                $teacherIds = $sched['teacher_ids'] ?? [];
                $subject->teachers()->sync($teacherIds);
                $subject->update(['teacher_id' => !empty($teacherIds) ? $teacherIds[0] : null]);
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui seluruh jadwal & mapel kelas {$class->name} secara massal",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Jadwal kelas {$class->name} berhasil diperbarui.");
    }

    /**
     * CRUD: Simpan pengguna baru (siswa/guru).
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:student,teacher,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat pengguna baru: {$user->name} ({$user->role})",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Pengguna {$user->name} ({$user->role}) berhasil ditambahkan.");
    }

    /**
     * CRUD: Perbarui data pengguna.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:student,teacher,admin'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $oldName = $user->name;
        $user->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui data pengguna {$oldName} menjadi {$user->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * CRUD: Hapus data pengguna.
     */
    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Menghapus pengguna {$userName}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Pengguna {$userName} berhasil dihapus.");
    }

    /**
     * Unduh template CSV untuk import pengguna.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_pengguna.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM untuk kompatibilitas Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['nama', 'email', 'password', 'role']);
            fputcsv($file, ['Ahmad Rahmat', 'ahmad.rahmat@almuhajirin.sch.id', 'password123', 'student']);
            fputcsv($file, ['Siti Aminah', 'siti.aminah@almuhajirin.sch.id', 'password123', 'teacher']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import pengguna massal dari berkas CSV/Excel.
     */
    public function importUsers(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuka file.');
        }

        // Lewati UTF-8 BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Deteksi delimiter: koma atau titik koma (Windows regional standard)
        $firstLine = fgets($handle);
        rewind($handle);
        if ($bom !== "\xEF\xBB\xBF") {
            fread($handle, 3);
        }
        
        $delimiter = ',';
        if ($firstLine !== false && strpos($firstLine, ';') !== false && (strpos($firstLine, ',') === false || strpos($firstLine, ';') < strpos($firstLine, ','))) {
            $delimiter = ';';
        }

        // Baca header
        $headers = fgetcsv($handle, 1000, $delimiter);
        if (!$headers || count($headers) < 4) {
            fclose($handle);
            return redirect()->back()->with('error', 'Format header file tidak valid. Pastikan header memiliki kolom: nama, email, password, role.');
        }

        $headers = array_map(function($h) {
            return strtolower(trim($h));
        }, $headers);

        $nameIdx = array_search('nama', $headers);
        $emailIdx = array_search('email', $headers);
        $passIdx = array_search('password', $headers);
        $roleIdx = array_search('role', $headers);

        if ($nameIdx === false || $emailIdx === false || $passIdx === false || $roleIdx === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'Format header kolom salah. Harus terdapat kolom: nama, email, password, role.');
        }

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;
            if (count($row) < 4) {
                continue;
            }

            $name = trim($row[$nameIdx] ?? '');
            $email = trim($row[$emailIdx] ?? '');
            $password = trim($row[$passIdx] ?? '');
            $role = strtolower(trim($row[$roleIdx] ?? ''));

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $errors[] = "Baris {$rowNum}: Kolom tidak boleh kosong.";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Baris {$rowNum}: Format email '{$email}' tidak valid.";
                continue;
            }

            if (!in_array($role, ['student', 'teacher', 'admin'])) {
                $errors[] = "Baris {$rowNum}: Role '{$role}' tidak dikenal (harus student, teacher, atau admin).";
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Baris {$rowNum}: Email '{$email}' sudah digunakan.";
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => $role,
            ]);

            $imported++;
        }

        fclose($handle);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Mengimpor {$imported} pengguna melalui file CSV",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        $message = "Berhasil mengimpor {$imported} pengguna.";
        if (count($errors) > 0) {
            $message .= " Beberapa baris dilewati: " . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " (...dan " . (count($errors) - 3) . " error lainnya)";
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Bersihkan seluruh riwayat log aktivitas sistem.
     */
    public function clearLogs(Request $request): RedirectResponse
    {
        ActivityLog::truncate();

        // Catat aktivitas pembersihan ke log baru
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Membersihkan seluruh log aktivitas sistem',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', 'Seluruh log aktivitas berhasil dibersihkan.');
    }

    /**
     * Menyimpan anggota kelas secara massal (batch save).
     */
    public function saveClassStudents(Request $request, $classId): RedirectResponse
    {
        $request->validate([
            'added_student_ids' => ['nullable', 'string'],
            'removed_student_ids' => ['nullable', 'string'],
        ]);

        $class = Classroom::findOrFail($classId);

        $addedIds = json_decode($request->added_student_ids, true) ?? [];
        $removedIds = json_decode($request->removed_student_ids, true) ?? [];

        if (!empty($removedIds)) {
            $class->students()->detach($removedIds);
        }

        if (!empty($addedIds)) {
            $class->students()->attach($addedIds);
        }

        $addedCount = count($addedIds);
        $removedCount = count($removedIds);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui anggota kelas {$class->name}: Menambahkan {$addedCount} siswa & mengeluarkan {$removedCount} siswa secara massal",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Anggota kelas {$class->name} berhasil diperbarui (Ditambah: {$addedCount}, Dikeluarkan: {$removedCount}).");
    }
}

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

    /**
     * Tampilkan halaman Kelola Pengguna.
     */
    public function usersIndex(): View
    {
        $users = User::orderBy('name')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Tampilkan halaman Kelola Kelas.
     */
    public function classesIndex(): View
    {
        $classes = Classroom::with(['homeroomTeacher', 'subjects'])->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        
        return view('admin.classes', compact('classes', 'teachers'));
    }

    /**
     * Tampilkan halaman Kelola Mapel & Jadwal.
     */
    public function subjectsIndex(): View
    {
        $subjects = Subject::with(['classroom', 'teacher'])->orderBy('name')->get();
        $classes = Classroom::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.subjects', compact('subjects', 'classes', 'teachers'));
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
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'day' => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        $subject = Subject::create([
            'name' => $request->name,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Membuat mata pelajaran baru: {$subject->name} untuk kelas {$subject->classroom->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Mata pelajaran {$subject->name} berhasil ditambahkan.");
    }

    /**
     * CRUD: Perbarui mata pelajaran.
     */
    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'day' => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        $oldName = $subject->name;
        $subject->update([
            'name' => $request->name,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Memperbarui mata pelajaran {$oldName} menjadi {$subject->name}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Mata pelajaran {$subject->name} berhasil diperbarui.");
    }

    /**
     * CRUD: Hapus mata pelajaran.
     */
    public function destroySubject(Request $request, Subject $subject): RedirectResponse
    {
        $subjectName = $subject->name;
        $subject->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => "Menghapus mata pelajaran {$subjectName}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->back()->with('success', "Mata pelajaran {$subjectName} berhasil dihapus.");
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
}

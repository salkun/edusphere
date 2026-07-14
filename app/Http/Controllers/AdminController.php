<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Admin.
     */
    public function index(): View
    {
        // 1. Ambil 50 log aktivitas terbaru
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);

        return view('admin', compact('logs'));
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
}

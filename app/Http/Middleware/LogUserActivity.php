<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->isSuccessful() || $response->isRedirection()) {
            $user = Auth::user();
            $path = $request->path();
            $method = $request->method();
            $activity = null;

            if ($method === 'GET' && !$request->expectsJson() && !str_starts_with($path, '_') && !str_starts_with($path, 'api') && !str_starts_with($path, 'sanctum')) {
                // Page views
                $activity = 'Membuka halaman ';
                if ($path === '/') $activity .= 'Beranda';
                elseif ($path === 'dashboard') $activity .= 'Dashboard';
                elseif ($path === 'my-class') $activity .= 'Kelas Saya';
                elseif ($path === 'materials') $activity .= 'Materi Belajar';
                elseif ($path === 'assignments') $activity .= 'Tugas';
                elseif (str_starts_with($path, 'assignments/')) $activity .= 'Detail Tugas';
                elseif ($path === 'announcements') $activity .= 'Pengumuman';
                elseif ($path === 'grades') $activity .= 'Nilai Rapor';
                elseif ($path === 'settings') $activity .= 'Pengaturan Tampilan';
                elseif ($path === 'profile') $activity .= 'Profil Saya';
                elseif ($path === 'admin') $activity .= 'Dashboard Admin';
                else $activity .= '/' . $path;
            } elseif ($method === 'POST') {
                if ($path === 'login') $activity = 'Login Berhasil';
                elseif ($path === 'logout') $activity = 'Logout Berhasil';
                elseif ($path === 'profile/avatar') $activity = 'Mengunggah Foto Profil Baru';
                elseif (str_contains($path, 'submissions')) $activity = 'Mengirimkan Pengumpulan Tugas';
            } elseif ($method === 'PATCH' && $path === 'profile') {
                $activity = 'Memperbarui Informasi Profil';
            } elseif ($method === 'PUT' && $path === 'password') {
                $activity = 'Mengubah Kata Sandi';
            }

            if ($activity) {
                try {
                    ActivityLog::create([
                        'user_id' => $user ? $user->id : null,
                        'activity' => $activity,
                        'url' => $request->fullUrl(),
                        'method' => $method,
                        'ip_address' => $request->ip() ?? '127.0.0.1',
                        'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                    ]);
                } catch (\Exception $e) {
                    // Abaikan jika pencatatan gagal demi mencegah crash
                }
            }
        }

        return $response;
    }
}

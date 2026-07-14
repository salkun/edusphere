<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Panel Kendali Admin</h1>
            <p class="text-slate-500 font-medium">Pantau aktivitas pengguna secara real-time dan kelola hak akses menu sidebar per role.</p>
        </div>
    </div>

    <!-- Alert Status -->
    @if (session('success'))
        <div class="mb-6 p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div x-data="{ activeTab: 'logs' }" class="space-y-6">
        <!-- TABS HEADER -->
        <div class="flex border-b border-slate-200 gap-6">
            <button @click="activeTab = 'logs'" 
                    :class="activeTab === 'logs' ? 'border-indigo-600 text-indigo-600 font-black' : 'border-transparent text-slate-400 font-bold hover:text-slate-600'"
                    class="pb-3 border-b-2 text-sm transition-all focus:outline-none">
                Log Aktivitas
            </button>
            <button @click="activeTab = 'sidebar'" 
                    :class="activeTab === 'sidebar' ? 'border-indigo-600 text-indigo-600 font-black' : 'border-transparent text-slate-400 font-bold hover:text-slate-600'"
                    class="pb-3 border-b-2 text-sm transition-all focus:outline-none">
                Konfigurasi Sidebar per Role
            </button>
        </div>

        <!-- TAB CONTENT: LOG AKTIVITAS -->
        <div x-show="activeTab === 'logs'" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-black text-slate-800">Riwayat Aktivitas Sistem</h2>
                    <p class="text-xs text-slate-450 mt-0.5">Catatan seluruh aksi login, view halaman, dan pengunggahan file secara real-time.</p>
                </div>
            </div>

            <div class="overflow-x-auto -mx-8">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                            <th class="px-8 py-4">Waktu</th>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Aktivitas</th>
                            <th class="px-6 py-4">Detail Request</th>
                            <th class="px-8 py-4">IP & Browser</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-8 py-4 text-xs font-bold text-slate-400">
                                    {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y - H:i:s') }} WIB
                                </td>
                                <td class="px-6 py-4">
                                    @if ($log->user)
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800">{{ $log->user->name }}</span>
                                            @if ($log->user->role === 'admin')
                                                <span class="text-[9px] font-bold bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded-full uppercase">Admin</span>
                                            @elseif ($log->user->role === 'teacher')
                                                <span class="text-[9px] font-bold bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded-full uppercase">Guru</span>
                                            @else
                                                <span class="text-[9px] font-bold bg-violet-50 text-violet-600 px-1.5 py-0.5 rounded-full uppercase">Siswa</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-slate-400 italic">Guest / Pengunjung</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black text-slate-800">
                                    {{ $log->activity }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold mr-1">{{ $log->method }}</span>
                                    {{ Str::after($log->url, request()->getSchemeAndHttpHost()) }}
                                </td>
                                <td class="px-8 py-4 text-xs text-slate-400">
                                    <div>IP: {{ $log->ip_address }}</div>
                                    <div class="text-[10px] truncate max-w-[150px] mt-0.5" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 font-bold text-slate-400 italic">Belum ada riwayat aktivitas log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>

        <!-- TAB CONTENT: KONFIGURASI SIDEBAR PER ROLE -->
        <div x-show="activeTab === 'sidebar'" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
            <div>
                <h2 class="text-lg font-black text-slate-800 mb-1">Konfigurasi Sidebar per Peran (Role)</h2>
                <p class="text-xs text-slate-450 mb-6 font-medium">Atur visibilitas menu navigasi secara berkelompok berdasarkan role. Tekan tombol <strong>Simpan Pengaturan Sidebar</strong> di bawah untuk menerapkan perubahan.</p>
            </div>

            <form action="{{ route('admin.settings.sidebar') }}" method="POST" class="space-y-6">
                @csrf
                <div class="overflow-x-auto -mx-8">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                                <th class="px-8 py-4 w-48">Peran (Role)</th>
                                <th class="px-6 py-4 text-center">Kelas Saya</th>
                                <th class="px-6 py-4 text-center">Materi</th>
                                <th class="px-6 py-4 text-center">Tugas</th>
                                <th class="px-6 py-4 text-center">Pengumuman</th>
                                <th class="px-6 py-4 text-center">Nilai Rapor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                            <!-- Role Siswa -->
                            <tr>
                                <td class="px-8 py-5">
                                    <span class="font-black text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                                        Siswa (Student)
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="student_menu_class_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('student_menu_class_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="student_menu_materials_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('student_menu_materials_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="student_menu_assignments_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('student_menu_assignments_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="student_menu_announcements_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('student_menu_announcements_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="student_menu_grades_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('student_menu_grades_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                            </tr>

                            <!-- Role Guru -->
                            <tr>
                                <td class="px-8 py-5">
                                    <span class="font-black text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        Guru (Teacher)
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="teacher_menu_class_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('teacher_menu_class_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="teacher_menu_materials_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('teacher_menu_materials_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="teacher_menu_assignments_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('teacher_menu_assignments_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="teacher_menu_announcements_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('teacher_menu_announcements_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="teacher_menu_grades_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('teacher_menu_grades_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                            </tr>

                            <!-- Role Admin -->
                            <tr>
                                <td class="px-8 py-5">
                                    <span class="font-black text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                        Admin (Administrator)
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="admin_menu_class_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('admin_menu_class_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="admin_menu_materials_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('admin_menu_materials_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="admin_menu_assignments_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('admin_menu_assignments_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="admin_menu_announcements_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('admin_menu_announcements_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="admin_menu_grades_visible" value="1" 
                                           {{ \App\Models\SystemSetting::get('admin_menu_grades_visible', true) ? 'checked' : '' }}
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-2xl shadow-md transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Pengaturan Sidebar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

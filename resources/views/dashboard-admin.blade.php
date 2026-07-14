<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Beranda Dashboard</h1>
            <p class="text-slate-500 font-medium font-sans">Selamat datang di Panel Utama Edusphere. Berikut adalah log aktivitas lalu lintas sistem terbaru secara real-time.</p>
        </div>
    </div>

    <!-- TABLE LOG AKTIVITAS -->
    <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-800">Riwayat Aktivitas Sistem</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium font-sans">Catatan seluruh aksi login, view halaman, dan pengunggahan file secara real-time.</p>
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
</x-app-layout>

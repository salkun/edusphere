<x-app-layout>
    <!-- Welcome Header Card -->
    <div class="mb-8 p-8 rounded-3xl bg-gradient-to-r from-violet-600 via-indigo-600 to-indigo-700 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
        <div class="absolute right-0 bottom-0 translate-x-10 translate-y-10 opacity-10 pointer-events-none">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
            </svg>
        </div>
        <div class="relative z-10">
            <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold bg-white/20 rounded-full backdrop-blur-sm mb-4">🏫 Portal Guru</span>
            <h1 class="text-3xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }} 👋</h1>
            <p class="text-white/80 font-medium">Semangat mengajar hari ini! Pantau keaktifan belajar siswa secara langsung di sini.</p>
        </div>
    </div>

    <!-- Live Activity & Rankings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Kolom Kiri: Live Activity Feed (Materi & Tugas) -->
        <div class="space-y-6">
            <!-- Materi Dibaca Feed -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Progres Membaca Materi (Terbaru)</h2>
                </div>

                @if($materialActivities->isEmpty())
                    <div class="py-6 text-center text-xs text-slate-450 dark:text-slate-500 italic">
                        Belum ada siswa yang membaca materi hari ini.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 space-y-3">
                        @foreach($materialActivities as $act)
                            <div class="pt-3 first:pt-0 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-250 truncate">{{ $act->student_name }}</div>
                                    <div class="text-[10px] text-slate-450 dark:text-slate-500 truncate mt-0.5">
                                        Membaca: <span class="font-semibold text-indigo-650 dark:text-indigo-400">{{ $act->material_title }}</span> &bull; Kelas {{ $act->class_name }}
                                    </div>
                                </div>
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($act->created_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tugas Dikumpulkan Feed -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2.5 h-5 bg-emerald-500 rounded-full"></span>
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Pengumpulan Tugas (Terbaru)</h2>
                </div>

                @if($submissionActivities->isEmpty())
                    <div class="py-6 text-center text-xs text-slate-450 dark:text-slate-500 italic">
                        Belum ada siswa yang mengumpulkan tugas hari ini.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 space-y-3">
                        @foreach($submissionActivities as $sub)
                            <div class="pt-3 first:pt-0 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-250 truncate">{{ $sub->student->name }}</div>
                                    <div class="text-[10px] text-slate-450 dark:text-slate-500 truncate mt-0.5">
                                        Tugas: <span class="font-semibold text-emerald-650 dark:text-emerald-400">{{ $sub->assignment->title }}</span> &bull; Kelas {{ $sub->assignment->subject->classroom->name ?? '-' }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1 whitespace-nowrap">
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-600">
                                        {{ $sub->created_at->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                    </span>
                                    <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded {{ $sub->status === 'graded' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                        {{ $sub->status === 'graded' ? 'Dinilai' : 'Menunggu' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Top 10 Student Rankings -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2.5 h-5 bg-amber-500 rounded-full"></span>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Top 10 Siswa Berprestasi (Mata Pelajaran Anda)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-55 border-b border-slate-100 dark:border-slate-800 text-slate-450 dark:text-slate-500 uppercase text-[9px] font-bold tracking-wider">
                            <th class="px-3 py-2 text-center w-10">Peringkat</th>
                            <th class="px-4 py-2">Nama Siswa</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2 text-center">Rata-Rata Nilai</th>
                            <th class="px-3 py-2 text-right">Progres Mapel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        @forelse($rankings as $idx => $r)
                            <tr>
                                <td class="px-3 py-2.5 text-center">
                                    @if($idx === 0)
                                        <span class="bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded text-[10px] font-black">🥇 1</span>
                                    @elseif($idx === 1)
                                        <span class="bg-slate-100 text-slate-750 px-1.5 py-0.5 rounded text-[10px] font-black">🥈 2</span>
                                    @elseif($idx === 2)
                                        <span class="bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded text-[10px] font-black">🥉 3</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600 font-bold">#{{ $idx + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-slate-800 dark:text-slate-200">
                                    {{ $r['name'] }}
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 font-semibold">
                                    {{ $r['class_name'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-black text-sm">{{ $r['average_grade'] }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-right text-emerald-600 dark:text-emerald-400">
                                    {{ $r['progress'] }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400 italic">Belum ada data progres/nilai dari kelas yang diampu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Grid Layout (Pie & Bar) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pie Chart Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Tingkat Pengumpulan Tugas per Kelas
            </h3>
            <div class="w-full relative flex items-center justify-center" style="height: 280px;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <!-- Bar Chart Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                Nilai Rerata Tugas Tertinggi per Kelas
            </h3>
            <div class="w-full relative flex items-center justify-center" style="height: 280px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ChartJS Scripts Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dark mode detection for chart grids and text
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#475569';
            const gridColor = isDark ? '#334155' : '#f1f5f9';

            // 1. Data & Konfigurasi Pie Chart
            const pieData = @json($pieData);
            const pieLabels = pieData.map(item => item.class_name);
            const pieSubmissions = pieData.map(item => item.submissions_count);
            
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieSubmissions,
                        backgroundColor: [
                            '#4f46e5', // indigo-600
                            '#10b981', // emerald-500
                            '#f59e0b', // amber-500
                            '#ec4899', // pink-500
                            '#06b6d4', // cyan-500
                            '#8b5cf6'  // violet-500
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#0f172a' : '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                font: {
                                    family: 'Outfit, Inter, sans-serif',
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const studentsCount = pieData[index].students_count;
                                    return ` ${context.label}: ${context.raw} pengumpulan (Total kelas: ${studentsCount} siswa)`;
                                }
                            }
                        }
                    }
                }
            });

            // 2. Data & Konfigurasi Bar Chart
            const barData = @json($barData);
            const barLabels = barData.map(item => item.class_name);
            const barGrades = barData.map(item => item.average_grade);

            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Rerata Nilai Tugas',
                        data: barGrades,
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 8,
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: 'Outfit, Inter, sans-serif',
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: 'Outfit, Inter, sans-serif',
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

<x-app-layout>
    <!-- Welcome Header Card -->
    <div class="mb-8 p-8 rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
        <div class="absolute right-0 bottom-0 translate-x-10 translate-y-10 opacity-10 pointer-events-none">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
            </svg>
        </div>
        <div class="relative z-10">
            <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold bg-white/20 rounded-full backdrop-blur-sm mb-4">📢 Pengumuman</span>
            <h1 class="text-3xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }} 👋</h1>
            <p class="text-white/80 font-medium">Semangat belajar hari ini! Tetap fokus meraih cita-citamu.</p>
        </div>
    </div>

    <!-- Main Dashboard Grid Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        
        <!-- Left 3 Columns: Active Classes, Upcoming Tasks, Recent Materials -->
        <div class="xl:col-span-3 space-y-8">
            
            <!-- Kelas Aktif Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">Kelas Aktif</h2>
                    <a href="#" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat semua</a>
                </div>
                
                @if($subjects->isEmpty())
                    <div class="p-6 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 text-slate-500">
                        Tidak ada mata pelajaran aktif.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($subjects as $subject)
                            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-200 flex flex-col justify-between min-h-[160px]">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $classroom->name }}</span>
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-full">{{ $subject->progress }}% Selesai</span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1">{{ $subject->name }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $subject->teacher->name ?? 'Guru Pengampu' }}</p>
                                </div>
                                <div class="mt-4">
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $subject->progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tugas Mendatang Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">Tugas Mendatang</h2>
                    <a href="#" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat semua</a>
                </div>

                @if($upcomingAssignments->isEmpty())
                    <div class="p-6 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 text-slate-500">
                        Tidak ada tugas mendatang.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingAssignments as $assignment)
                            @php
                                $now = \Carbon\Carbon::now();
                                $deadline = $assignment->deadline;
                                if ($deadline->isTomorrow()) {
                                    $deadlineText = 'Besok, ' . $deadline->format('H:i');
                                } elseif ($deadline->isToday()) {
                                    $deadlineText = 'Hari ini, ' . $deadline->format('H:i');
                                } else {
                                    $deadlineText = $deadline->diffForHumans($now, [
                                        'parts' => 2,
                                        'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW
                                    ]);
                                }
                                
                                // Map types for tag styles
                                $tagStyle = match($assignment->type) {
                                    'coding' => 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/50',
                                    'file' => 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/50',
                                    'essay' => 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-900/50',
                                    default => 'bg-slate-50 dark:bg-slate-950/30 text-slate-600 dark:text-slate-400 border-slate-100 dark:border-slate-900/50'
                                };
                            @endphp
                            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 border rounded {{ $tagStyle }}">{{ $assignment->type }}</span>
                                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase">{{ $assignment->subject->name }} &bull; {{ $assignment->subject->teacher->name }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 truncate">{{ $assignment->title }}</h3>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-6 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800">
                                    <div class="text-left sm:text-right">
                                        <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold">TENGGAT WAKTU</div>
                                        <div class="text-sm font-bold text-rose-500 dark:text-rose-400">{{ $deadlineText }}</div>
                                    </div>
                                    <a href="#" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all duration-150 rounded-xl shadow-md shadow-indigo-500/10">Kerjakan</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Materi Terbaru Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">Materi Terbaru</h2>
                    <a href="#" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat semua</a>
                </div>

                @if($recentMaterials->isEmpty())
                    <div class="p-6 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 text-slate-500">
                        Tidak ada materi terbaru.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($recentMaterials as $material)
                            @php
                                $isCompleted = in_array($material->id, $completedMaterialIds);
                            @endphp
                            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-slate-350 transition-all duration-200 flex flex-col justify-between min-h-[180px]">
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                                            </svg>
                                        </span>
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $material->subject->name }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1 leading-snug line-clamp-2">{{ $material->title }}</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase">{{ $material->subject->teacher->name }}</p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                    <form method="POST" action="{{ route('materials.toggle', $material->id) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors duration-150 {{ $isCompleted ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'text-indigo-600 border-indigo-200 dark:border-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 dark:text-indigo-400' }}">
                                            {{ $isCompleted ? '✓ Selesai' : 'Tandai Selesai' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Column: Progress & Streak Panels -->
        <div class="space-y-8">
            
            <!-- Progress Belajar Card -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm text-center">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Progress Belajar</h3>
                    <a href="#" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat detail</a>
                </div>
                
                <!-- Circular Progress Bar UI Wrapper -->
                <div class="relative flex items-center justify-center w-36 h-36 mx-auto mb-6">
                    <!-- SVG Circle -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle -->
                        <circle class="text-slate-100 dark:text-slate-800" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
                        <!-- Foreground Circle (Dynamic Progress) -->
                        <circle class="text-indigo-600 dark:text-indigo-400 transition-all duration-500" 
                                stroke-width="8" 
                                stroke-dasharray="251.2" 
                                stroke-dashoffset="{{ 251.2 * (1 - $globalProgress / 100) }}" 
                                stroke-linecap="round" 
                                stroke="currentColor" 
                                fill="transparent" 
                                r="40" cx="50" cy="50"/>
                    </svg>
                    <div class="absolute text-center">
                        <span class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $globalProgress }}%</span>
                    </div>
                </div>

                <h4 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1">Total Progress</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 px-4 leading-normal">
                    Kamu telah menyelesaikan {{ $completedClassMaterialsCount }} dari {{ $totalClassMaterialsCount }} materi pembelajaran
                </p>
            </div>

            <!-- Streak Belajar Card -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm text-center relative overflow-hidden">
                <!-- Decorative Top Sparkles -->
                <div class="absolute -right-4 -top-4 w-12 h-12 bg-amber-500/10 rounded-full blur-xl"></div>
                
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🔥</span>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Streak Belajar</span>
                </div>
                
                <div class="text-left">
                    <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 mb-1">0 hari</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pertahankan konsistensimu! Selesaikan minimal 1 materi/tugas setiap hari untuk mengumpulkan streak.</p>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

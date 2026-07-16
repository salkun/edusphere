<x-app-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Jadwal Pelajaran</h1>
            <p class="text-xs font-bold text-indigo-650">Lihat jadwal mengajar mandiri maupun jadwal kelas umum sekolah</p>
        </div>
        <form method="GET" action="{{ route('teacher.schedule') }}" class="flex items-center gap-2">
            <label for="filter" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Filter:</label>
            <select name="filter" id="filter" onchange="this.form.submit()" 
                    class="border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                <option value="self" {{ $filter === 'self' ? 'selected' : '' }}>Jadwal Saya (Mengajar)</option>
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Semua Jadwal Kelas</option>
            </select>
        </form>
    </div>

    @php
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    @endphp

    @if ($filter === 'self')
        <!-- JADWAL SAYA VIEW -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($days as $day)
                @php
                    $daySubjects = $mySubjects->filter(fn($sub) => $sub->day === $day)->sortBy('start_time');
                @endphp
                <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                        <span class="text-base font-black text-slate-805 dark:text-slate-150">{{ $day }}</span>
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 dark:bg-slate-850 px-2 py-0.5 rounded-full">{{ $daySubjects->count() }} Jam Mengajar</span>
                    </div>

                    @if ($daySubjects->isEmpty())
                        <div class="py-8 text-center text-xs text-slate-400 italic">Tidak ada jadwal mengajar.</div>
                    @else
                        <div class="space-y-3">
                            @foreach ($daySubjects as $sub)
                                <div class="p-3.5 bg-slate-50/50 dark:bg-slate-850/30 border border-slate-150 dark:border-slate-800 rounded-2xl flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[10px] font-black uppercase text-indigo-650 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded">
                                                {{ $sub->classroom->name ?? '-' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400">
                                                {{ \Carbon\Carbon::parse($sub->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sub->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-205">{{ $sub->name }}</h4>
                                    </div>
                                    
                                    <!-- Rekan Guru Lainnya (jika ada) -->
                                    @php
                                        $coTeachers = $sub->teachers->filter(fn($t) => $t->id !== Auth::id());
                                    @endphp
                                    @if($coTeachers->isNotEmpty())
                                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Rekan Pengampu:</div>
                                            <div class="text-[9px] font-bold text-slate-500">{{ $coTeachers->pluck('name')->implode(', ') }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    @else
        <!-- SEMUA JADWAL KELAS VIEW -->
        <div class="space-y-6" x-data="{ activeClass: '' }">
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-4">
                @foreach ($allClasses as $idx => $cr)
                    <button type="button" 
                            @click="activeClass = 'class-{{ $cr->id }}'"
                            :class="activeClass === 'class-{{ $cr->id }}' || (activeClass === '' && {{ $idx === 0 ? 'true' : 'false' }}) ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black transition-all">
                        {{ $cr->name }}
                    </button>
                @endforeach
            </div>

            @foreach ($allClasses as $idx => $cr)
                <div x-show="activeClass === 'class-{{ $cr->id }}' || (activeClass === '' && {{ $idx === 0 ? 'true' : 'false' }})" class="space-y-4" x-cloak>
                    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-150">Jadwal Kelas: {{ $cr->name }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold">Wali Kelas: {{ $cr->homeroomTeacher->name ?? 'Belum Ditentukan' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($days as $day)
                                @php
                                    $daySubjects = $cr->subjects->filter(fn($sub) => $sub->day === $day)->sortBy('start_time');
                                @endphp
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-850/20 border border-slate-200/40 dark:border-slate-800 rounded-2xl">
                                    <div class="font-black text-slate-750 dark:text-slate-250 text-xs border-b border-slate-150/60 dark:border-slate-800 pb-2 mb-3 flex items-center justify-between">
                                        <span>{{ $day }}</span>
                                        <span class="text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded">{{ $daySubjects->count() }} Mapel</span>
                                    </div>

                                    @if ($daySubjects->isEmpty())
                                        <div class="py-6 text-center text-[10px] text-slate-400 italic">Belum ada mata pelajaran.</div>
                                    @else
                                        <div class="space-y-2">
                                            @foreach ($daySubjects as $sub)
                                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="text-[9px] font-bold text-slate-400">
                                                            {{ \Carbon\Carbon::parse($sub->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sub->end_time)->format('H:i') }}
                                                        </span>
                                                    </div>
                                                    <h5 class="text-xs font-black text-slate-800 dark:text-slate-200 mb-1">{{ $sub->name }}</h5>
                                                    <div class="text-[9px] text-slate-500 font-bold">
                                                        Guru: {{ $sub->teachers->pluck('name')->implode(', ') ?: 'Belum Ditentukan' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>

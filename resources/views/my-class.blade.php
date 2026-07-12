<x-app-layout>
    <!-- Header Card Kelas -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Informasi Kelas</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Kelas {{ $classroom->name ?? '-' }}</h1>
            <p class="text-slate-500 font-medium">Berikut adalah informasi kelas aktif Anda beserta wali kelas dan jadwal pelajaran mingguan.</p>
        </div>
        
        <!-- Wali Kelas & Statistik Card -->
        <div class="flex items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-100 shrink-0">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                {{ $classroom && $classroom->homeroomTeacher ? substr($classroom->homeroomTeacher->name, 3, 1) : 'W' }}
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold tracking-wider uppercase">WALI KELAS</div>
                <div class="text-base font-bold text-slate-800">{{ $classroom->homeroomTeacher->name ?? 'Belum Ditentukan' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Guru Pengampu & Pembimbing Kelas</div>
            </div>
        </div>
    </div>

    <!-- Jadwal Pelajaran Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Jadwal Mata Pelajaran</h2>
            <span class="text-sm font-semibold text-slate-400">{{ $subjects->count() }} Mata Pelajaran</span>
        </div>

        @if($subjects->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200/60 text-slate-500">
                Tidak ada jadwal pelajaran untuk kelas Anda saat ini.
            </div>
        @else
            @php
                // Grouping subjects by day
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $groupedSubjects = $subjects->groupBy('day');
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($days as $day)
                    @php
                        $daySubjects = $groupedSubjects->get($day, collect());
                    @endphp
                    
                    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden flex flex-col min-h-[300px]">
                        <!-- Day Header -->
                        <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-150 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800">{{ $day }}</h3>
                            <span class="text-xs font-semibold px-2 py-0.5 bg-slate-200 text-slate-600 rounded-md">
                                {{ $daySubjects->count() }} Pelajaran
                            </span>
                        </div>
                        
                        <!-- Subjects List -->
                        <div class="flex-1 p-6 space-y-4">
                            @if($daySubjects->isEmpty())
                                <div class="h-full flex flex-col items-center justify-center text-center py-8">
                                    <span class="text-3xl mb-2">🍃</span>
                                    <p class="text-xs font-medium text-slate-400">Tidak ada jadwal pelajaran</p>
                                </div>
                            @else
                                @foreach($daySubjects as $subject)
                                    @php
                                        // Formatting time
                                        $startTime = \Carbon\Carbon::parse($subject->start_time)->format('H:i');
                                        $endTime = \Carbon\Carbon::parse($subject->end_time)->format('H:i');
                                    @endphp
                                    <div class="p-4 bg-slate-50/60 hover:bg-slate-50 border border-slate-100 rounded-2xl transition-all duration-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                                                {{ $startTime }} - {{ $endTime }} WIB
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 mb-0.5 leading-snug">{{ $subject->name }}</h4>
                                        <p class="text-xs text-slate-500 font-medium">{{ $subject->teacher->name ?? 'Guru Pengampu' }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

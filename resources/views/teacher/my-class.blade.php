<x-app-layout>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Monitoring Kelas Saya</h1>
        <p class="text-xs font-bold text-indigo-650">Monitoring progres belajar dan sebaran nilai siswa di kelas perwalian Anda</p>
    </div>

    <!-- Classroom Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Nama Kelas Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nama Kelas</div>
                <div class="text-lg font-black text-slate-800 dark:text-slate-100">{{ $classroom->name }}</div>
            </div>
        </div>

        <!-- Jumlah Siswa Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Anggota Kelas</div>
                <div class="text-lg font-black text-slate-800 dark:text-slate-100">{{ $classroom->students->count() }} Siswa</div>
            </div>
        </div>

        <!-- Jumlah Mapel Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-violet-55 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Mata Pelajaran</div>
                <div class="text-lg font-black text-slate-800 dark:text-slate-100">{{ $subjects->count() }} Mapel</div>
            </div>
        </div>
    </div>

    <!-- Grade Book Matrix Table Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
            <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
            Matriks Penilaian & Progres Belajar Siswa
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-55 dark:bg-slate-850/50 border-b border-slate-150 dark:border-slate-800 text-slate-450 dark:text-slate-500 uppercase text-[9px] font-bold tracking-wider">
                        <th class="px-4 py-3 w-10 text-center">No</th>
                        <th class="px-4 py-3 min-w-[150px]">Nama Siswa</th>
                        <!-- Render dynamic subject header columns -->
                        @foreach ($subjects as $subj)
                            <th class="px-3 py-3 text-center min-w-[120px]">{{ $subj->name }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right min-w-[120px]">Progres Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-350 font-bold">
                    @forelse ($studentsData as $idx => $student)
                        <tr>
                            <td class="px-4 py-3 text-center text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-black text-slate-800 dark:text-slate-200">{{ $student['name'] }}</div>
                                <div class="text-[9px] text-slate-400 font-normal mt-0.5">{{ $student['email'] }}</div>
                            </td>
                            <!-- Render student grades for each subject -->
                            @foreach ($subjects as $subj)
                                @php
                                    $score = $student['grades'][$subj->id];
                                    $scoreStyle = 'text-slate-400 font-medium';
                                    if ($score !== '-') {
                                        if ($score >= 75) {
                                            $scoreStyle = 'text-emerald-600 dark:text-emerald-400 font-black';
                                        } else {
                                            $scoreStyle = 'text-amber-500 font-black';
                                        }
                                    }
                                @endphp
                                <td class="px-3 py-3 text-center {{ $scoreStyle }}">
                                    {{ $score }}
                                </td>
                            @endforeach
                            <!-- Progress Bar -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <span class="text-[10px] text-indigo-650 dark:text-indigo-400">{{ $student['progress'] }}%</span>
                                    <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden shrink-0">
                                        <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $student['progress'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $subjects->count() }}" class="text-center py-10 text-slate-400 italic">
                                Belum ada siswa terdaftar di kelas perwalian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

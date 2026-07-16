<x-app-layout>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Input Nilai Siswa</h1>
        <p class="text-xs font-bold text-indigo-650">Kelola dan berikan penilaian tugas untuk mata pelajaran yang Anda ampu</p>
    </div>

    <!-- Assignment Selector -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm mb-6">
        <form method="GET" action="{{ route('teacher.grades.index') }}" class="max-w-xl">
            <label for="assignment_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Tugas / Ujian:</label>
            <select name="assignment_id" id="assignment_id" onchange="this.form.submit()"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                <option value="">-- Pilih Tugas --</option>
                @foreach ($mySubjects as $sub)
                    @if ($sub->assignments->isNotEmpty())
                        <optgroup label="{{ $sub->name }} (Kelas {{ $sub->classroom->name ?? '-' }})">
                            @foreach ($sub->assignments as $assign)
                                <option value="{{ $assign->id }}" {{ $selectedAssignmentId == $assign->id ? 'selected' : '' }}>
                                    {{ $assign->title }} (Tenggat: {{ \Carbon\Carbon::parse($assign->deadline)->translatedFormat('d M Y, H:i') }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
        </form>
    </div>

    @if ($selectedAssignment)
        <!-- Form Simpan Nilai Massal -->
        <form method="POST" action="{{ route('teacher.grades.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $selectedAssignment->id }}">

            <!-- Assignment Info Card -->
            <div class="p-5 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] font-black uppercase text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                        {{ $selectedAssignment->subject->classroom->name ?? '-' }} &bull; {{ $selectedAssignment->subject->name }}
                    </span>
                    <h3 class="text-base font-black text-slate-800 mt-1.5">{{ $selectedAssignment->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium">{{ $selectedAssignment->description }}</p>
                </div>
                <div class="text-xs text-slate-500 font-bold border-t md:border-t-0 pt-2 md:pt-0 border-slate-100 shrink-0">
                    <div>TENGGAT WAKTU:</div>
                    <div class="text-rose-500 font-black text-sm">{{ \Carbon\Carbon::parse($selectedAssignment->deadline)->translatedFormat('d M Y, H:i') }}</div>
                </div>
            </div>

            <!-- Student List Grading Table -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-emerald-500 rounded-full"></span>
                    Daftar Nilai Siswa ({{ $students->count() }} Anggota Kelas)
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-55 border-b border-slate-100 text-slate-450 uppercase text-[9px] font-bold tracking-wider">
                                <th class="px-4 py-3 w-10 text-center">No</th>
                                <th class="px-4 py-3 min-w-[150px]">Nama Siswa</th>
                                <th class="px-4 py-3 min-w-[200px]">Status & Pengumpulan</th>
                                <th class="px-4 py-3 w-32 text-center">Skor (0-100)</th>
                                <th class="px-4 py-3 min-w-[220px]">Umpan Balik (Feedback)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-bold">
                            @foreach ($students as $idx => $student)
                                @php
                                    $sub = $submissions->get($student->id);
                                    $hasSubmitted = $sub && $sub->status !== 'draft';
                                    $grade = $sub ? $sub->grade : null;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-center text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-slate-800 dark:text-slate-200">{{ $student->name }}</div>
                                        <div class="text-[9px] text-slate-400 font-normal mt-0.5">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($hasSubmitted)
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span class="text-emerald-600">Sudah Mengumpulkan</span>
                                                </div>
                                                <div class="text-[9px] text-slate-450 font-normal">
                                                    Dikirim: {{ $sub->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                                </div>
                                                @if ($sub->content)
                                                    <div class="text-[10px] text-slate-600 font-medium bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-16 overflow-y-auto">
                                                        {{ $sub->content }}
                                                    </div>
                                                @endif
                                                @if ($sub->file_path)
                                                    <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank"
                                                       class="text-[9px] font-black text-indigo-600 hover:text-indigo-850 hover:underline flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Buka Berkas Lampiran
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 text-rose-500">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                <span>Belum Mengumpulkan</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" 
                                               name="grades[{{ $student->id }}][score]" 
                                               value="{{ $grade ? intval($grade->score) : '' }}"
                                               min="0" max="100" placeholder="--"
                                               class="w-20 border border-slate-200 rounded-xl px-2 py-1.5 text-center text-xs font-black focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" 
                                               name="grades[{{ $student->id }}][feedback]" 
                                               value="{{ $grade ? $grade->feedback : '' }}"
                                               placeholder="Umpan balik opsional..."
                                               class="w-full border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-slate-100">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-6 py-3 text-xs font-black transition-all shadow-md">
                        Simpan Semua Nilai
                    </button>
                </div>
            </div>
        </form>
    @else
        <!-- State Belum Memilih Tugas -->
        <div class="p-8 text-center bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl text-slate-500">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <div class="text-sm font-bold text-slate-700 dark:text-slate-350">Silakan Pilih Tugas Terlebih Dahulu</div>
            <p class="text-xs text-slate-400 mt-1">Gunakan pemilih dropdown di atas untuk memuat daftar tugas dan siswa yang akan dinilai.</p>
        </div>
    @endif
</x-app-layout>

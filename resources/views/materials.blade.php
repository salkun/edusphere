<x-app-layout>
    <!-- Header Card Materi -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Materi Belajar</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Materi Pelajaran - Kelas {{ $classroom->name ?? '-' }}</h1>
            <p class="text-slate-500 font-medium">Klik mata pelajaran di bawah untuk melihat dan membuka modul atau dokumen materi pelajaran.</p>
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

    <!-- Accordion List Pelajaran & Materi -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-slate-800">Daftar Mata Pelajaran</h2>

        @if($subjects->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200/60 text-slate-500">
                Tidak ada mata pelajaran di kelas Anda.
            </div>
        @else
            <div x-data="{ activeSubject: null }" class="space-y-4">
                @foreach($subjects as $subject)
                    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden transition-all duration-200">
                        <!-- Accordion Trigger Button -->
                        <button @click="activeSubject = activeSubject === {{ $subject->id }} ? null : {{ $subject->id }}" 
                                class="w-full flex items-center justify-between p-6 hover:bg-slate-50/50 transition-colors duration-150 text-left focus:outline-none">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">{{ $subject->name }}</h3>
                                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mt-0.5">{{ $subject->teacher->name ?? 'Guru Pengampu' }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full">
                                    {{ $subject->materials->count() }} Materi
                                </span>
                                <!-- Arrow Icon -->
                                <svg class="w-5 h-5 text-slate-450 transition-transform duration-200" 
                                     :class="activeSubject === {{ $subject->id }} ? 'rotate-180' : ''" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Materials Dropdown Panel -->
                        <div x-show="activeSubject === {{ $subject->id }}" 
                             x-collapse 
                             class="border-t border-slate-100 bg-slate-50/30 p-6 space-y-4"
                             style="display: none;">
                             
                            @if($subject->materials->isEmpty())
                                <div class="text-center py-6 text-sm text-slate-400 font-medium">
                                    Belum ada materi pelajaran yang diunggah oleh guru pengampu.
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($subject->materials as $material)
                                        <a href="{{ $material->file_path ?? '#' }}" 
                                           target="_blank" 
                                           class="p-5 bg-white border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200 flex items-start gap-4">
                                            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl shrink-0">
                                                <!-- PDF Document Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-bold text-slate-800 truncate mb-2">{{ $material->title }}</h4>
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-650 hover:underline">
                                                    <span>Buka File Modul</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

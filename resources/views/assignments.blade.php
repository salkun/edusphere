<x-app-layout>
    <!-- Header Card Tugas -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Tugas Sekolah</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Daftar Tugas - Kelas {{ $classroom->name ?? '-' }}</h1>
            <p class="text-slate-500 font-medium">Berikut adalah seluruh tugas yang diberikan untuk kelas Anda beserta status pengumpulannya.</p>
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

    <!-- List Tugas -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Daftar Tugas Anda</h2>
            <span class="text-sm font-semibold text-slate-400">{{ $assignments->count() }} Total Tugas</span>
        </div>

        @if($assignments->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200/60 text-slate-500">
                Tidak ada tugas yang ditugaskan ke kelas Anda saat ini.
            </div>
        @else
            <div class="space-y-4">
                @foreach($assignments as $assignment)
                    @php
                        $isSubmitted = in_array($assignment->id, $submittedAssignmentIds);
                        
                        $now = \Carbon\Carbon::now();
                        $deadline = \Carbon\Carbon::parse($assignment->deadline);
                        $deadlineText = $deadline->translatedFormat('d M Y, H:i');
                        
                        // Map types for tag styles
                        $tagStyle = match($assignment->type) {
                            'coding' => 'bg-emerald-50 text-emerald-600 border-emerald-150',
                            'file' => 'bg-blue-50 text-blue-600 border-blue-150',
                            'essay' => 'bg-amber-50 text-amber-600 border-amber-150',
                            default => 'bg-slate-50 text-slate-600 border-slate-150'
                        };
                    @endphp
                    
                    <div class="p-5 bg-white border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 border rounded {{ $tagStyle }}">{{ $assignment->type }}</span>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $assignment->subject->name }} &bull; {{ $assignment->subject->teacher->name }}</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 truncate">{{ $assignment->title }}</h3>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-1 leading-normal">{{ $assignment->description }}</p>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            <div class="text-left sm:text-right">
                                <div class="text-xs text-slate-400 font-semibold tracking-wide">TENGGAT WAKTU</div>
                                <div class="text-sm font-bold {{ $deadline->isPast() && !$isSubmitted ? 'text-slate-400' : 'text-rose-500' }}">{{ $deadlineText }}</div>
                            </div>
                            
                            @if($isSubmitted)
                                <button disabled class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl cursor-default inline-flex items-center gap-1 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Dikumpulkan
                                </button>
                            @elseif($deadline->isPast())
                                <button disabled class="px-5 py-2.5 text-sm font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                                    Tenggat Terlewati
                                </button>
                            @else
                                <a href="{{ route('assignments.show', $assignment->id) }}" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all duration-150 rounded-xl shadow-md shadow-indigo-500/10">
                                    Kerjakan
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

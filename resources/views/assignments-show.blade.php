<x-app-layout>
    <!-- Back to Assignments Navigation -->
    <div class="mb-6">
        <a href="{{ route('assignments') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Tugas
        </a>
    </div>

    <!-- Main Grid: Left (Details & Form), Right (Status Info) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Left 2 Columns: Task Details & Submission Form -->
        <div class="xl:col-span-2 space-y-8">
            
            <!-- Assignment Detail Card -->
            <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
                @php
                    // Map types for tag styles
                    $tagStyle = match($assignment->type) {
                        'coding' => 'bg-emerald-50 text-emerald-600 border-emerald-150',
                        'file' => 'bg-blue-50 text-blue-600 border-blue-150',
                        'essay' => 'bg-amber-50 text-amber-600 border-amber-150',
                        default => 'bg-slate-50 text-slate-600 border-slate-150'
                    };
                @endphp
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 border rounded-lg {{ $tagStyle }}">{{ $assignment->type }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $assignment->subject->name }}</span>
                </div>
                
                <h1 class="text-2xl font-black text-slate-800 mb-3">{{ $assignment->title }}</h1>
                <p class="text-slate-500 font-medium text-xs uppercase mb-6">GURU PENGAMPU: {{ $assignment->subject->teacher->name }}</p>
                
                <div class="prose prose-slate max-w-none text-slate-650 mb-8 leading-relaxed">
                    <p class="whitespace-pre-line">{{ $assignment->description }}</p>
                </div>

                <!-- Teacher Attachment (If any) -->
                @if($assignment->file_path)
                    <a href="{{ $assignment->file_path }}" 
                       target="_blank" 
                       class="mt-6 p-5 bg-slate-50 border border-slate-150 hover:bg-slate-100/70 hover:border-slate-350 rounded-2xl flex items-center gap-4 transition-all duration-150">
                        <div class="p-2.5 bg-rose-50 text-rose-600 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-bold text-slate-800 truncate">Lampiran Dokumen Soal</div>
                            <div class="text-xs text-slate-450 mt-0.5">Format: Dokumen PDF / Pendukung Tugas &bull; Klik untuk membuka/mengunduh</div>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Submission Area -->
            <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Lembar Pengumpulan Tugas</h2>

                @if($submission)
                    <!-- Display Submitted Info -->
                    <div class="p-6 bg-emerald-50/50 border border-emerald-100 rounded-2xl mb-6">
                        <div class="flex items-center gap-2 text-emerald-700 font-bold text-sm mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tugas Anda Sudah Dikumpulkan
                        </div>
                        <p class="text-xs text-emerald-600">Dikirim pada: {{ \Carbon\Carbon::parse($submission->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                    </div>

                    <div class="space-y-6">
                        @if($submission->content)
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Jawab Teks / Essay Anda</label>
                                <div class="p-4 bg-slate-50 border border-slate-150 rounded-xl text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                    {{ $submission->content }}
                                </div>
                            </div>
                        @endif

                        @if($submission->file_path)
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Berkas yang Anda Upload</label>
                                <div class="p-4 bg-slate-50 border border-slate-150 rounded-xl flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-750 truncate">File Lampiran Jawaban</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                                        Lihat File
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Submission Form -->
                    <form method="POST" action="{{ route('assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Essay Text Area -->
                        <div>
                            <label for="content" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jawaban Teks / Essay</label>
                            <textarea id="content" name="content" rows="6" 
                                      placeholder="Tuliskan jawaban Anda di sini jika berupa essay..." 
                                      class="w-full p-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 placeholder-slate-400 leading-relaxed"></textarea>
                            @error('content')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Unggah File Lampiran 
                                @if($assignment->type === 'file' || $assignment->type === 'coding')
                                    <span class="text-rose-500 font-semibold">* (Sangat disarankan)</span>
                                @else
                                    <span class="text-slate-400 font-medium">(Opsional)</span>
                                @endif
                            </label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100/50 transition-colors duration-150">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mb-1 text-xs text-slate-500 font-semibold">Klik untuk memilih file jawaban</p>
                                        <p class="text-[10px] text-slate-400">PDF, ZIP, PNG, JPG (Maks. 10MB)</p>
                                    </div>
                                    <input id="file" name="file" type="file" class="hidden" />
                                </label>
                            </div>
                            @error('file')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-98 transition-all duration-150 rounded-xl shadow-md shadow-indigo-500/10">
                                Kumpulkan Tugas
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        </div>

        <!-- Right 1 Column: Deadline and Status Info -->
        <div class="space-y-6">
            
            <!-- Information Card -->
            <div class="p-6 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-6">Status Pengumpulan</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-xs font-semibold text-slate-400 uppercase">STATUS</span>
                        @if($submission)
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Selesai</span>
                        @else
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Belum Selesai</span>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-xs font-semibold text-slate-400 uppercase">TENGGAT</span>
                        <span class="text-xs font-bold text-rose-500">{{ \Carbon\Carbon::parse($assignment->deadline)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">JENIS TUGAS</span>
                        <span class="text-xs font-bold text-slate-700 capitalize">{{ $assignment->type }}</span>
                    </div>
                </div>
            </div>

            <!-- Learning Guide Hint -->
            <div class="p-6 bg-indigo-50/50 border border-indigo-100 rounded-3xl">
                <span class="text-xl mb-3 block">💡</span>
                <h4 class="text-sm font-bold text-indigo-900 mb-1">Panduan Pengumpulan</h4>
                <p class="text-xs text-indigo-750 leading-relaxed">
                    Pastikan Anda mengisi jawaban dengan teliti. Mengumpulkan tugas ini akan langsung memperbarui **Progres Belajar** Anda di dashboard!
                </p>
            </div>

        </div>

    </div>
</x-app-layout>

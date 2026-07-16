<x-app-layout>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Kelola Materi Belajar</h1>
        <p class="text-xs font-bold text-indigo-650">Unggah materi pelajaran, buku panduan, atau tautan video referensi belajar siswa</p>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Form Unggah Materi Baru -->
        <div class="lg:col-span-1">
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                    Tambah Materi Baru
                </h2>

                <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Pilih Mapel & Kelas -->
                    <div>
                        <label for="subject_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mata Pelajaran (Kelas):</label>
                        <select name="subject_id" id="subject_id" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach ($mySubjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} (Kelas {{ $sub->classroom->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Judul Materi -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Materi:</label>
                        <input type="text" name="title" id="title" required placeholder="Tulis judul materi..."
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Isi Materi (Teks/Deskripsi) -->
                    <div>
                        <label for="content" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konten / Ringkasan Materi:</label>
                        <textarea name="content" id="content" rows="5" placeholder="Tulis deskripsi atau isi materi singkat..."
                                  class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Unggah File Referensi -->
                    <div>
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">File Lampiran (PDF/Doc/Zip):</label>
                        <input type="file" name="file" id="file"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-500 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                        <p class="text-[9px] text-slate-400 mt-1 font-medium">Format: PDF, Word, Excel, Slide, Zip. Maks 10MB.</p>
                    </div>

                    <!-- Tautan Video YouTube/Lainnya -->
                    <div>
                        <label for="video_url" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tautan Video Referensi (Opsional):</label>
                        <input type="url" name="video_url" id="video_url" placeholder="https://example.com/video"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl py-3 text-xs font-black transition-all shadow-md">
                        Unggah Materi
                    </button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Materi Aktif Grouped by Mapel -->
        <div class="lg:col-span-2 space-y-6" x-data="{ activeMapel: '' }">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($mySubjects as $idx => $sub)
                    <button type="button" 
                            @click="activeMapel = 'subject-{{ $sub->id }}'"
                            :class="activeMapel === 'subject-{{ $sub->id }}' || (activeMapel === '' && {{ $idx === 0 ? 'true' : 'false' }}) ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black transition-all truncate">
                        {{ $sub->name }} ({{ $sub->classroom->name ?? '-' }})
                    </button>
                @endforeach
            </div>

            @foreach ($mySubjects as $idx => $sub)
                <div x-show="activeMapel === 'subject-{{ $sub->id }}' || (activeMapel === '' && {{ $idx === 0 ? 'true' : 'false' }})" class="space-y-4" x-cloak>
                    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-150">Daftar Materi: {{ $sub->name }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold">Kelas: {{ $sub->classroom->name ?? '-' }}</p>
                            </div>
                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-full">
                                {{ $sub->materials->count() }} Materi
                            </span>
                        </div>

                        @if ($sub->materials->isEmpty())
                            <div class="py-12 text-center text-xs text-slate-400 italic">
                                Belum ada materi belajar yang diunggah untuk kelas ini.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($sub->materials as $mat)
                                    <div class="p-4 bg-slate-50/50 dark:bg-slate-850/20 border border-slate-200/50 dark:border-slate-800 rounded-2xl">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <h4 class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $mat->title }}</h4>
                                            <span class="text-[9px] text-slate-400 font-semibold whitespace-nowrap">
                                                Dibuat: {{ $mat->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                                            </span>
                                        </div>

                                        @if ($mat->content)
                                            <p class="text-[11px] text-slate-600 dark:text-slate-400 font-medium mb-3 leading-relaxed">
                                                {{ $mat->content }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-3">
                                            @if ($mat->file_path)
                                                <a href="{{ asset('storage/' . $mat->file_path) }}" target="_blank"
                                                   class="text-[9px] font-black text-indigo-600 hover:text-indigo-850 hover:underline flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Unduh Dokumen Lampiran
                                                </a>
                                            @endif

                                            @if ($mat->video_url)
                                                <a href="{{ $mat->video_url }}" target="_blank"
                                                   class="text-[9px] font-black text-red-650 hover:text-red-800 hover:underline flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l-4 4m0 0l-4-4m4 4V4m0 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Buka Video Referensi
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

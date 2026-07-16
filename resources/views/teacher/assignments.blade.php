<x-app-layout>
    <div x-data="{ 
        openCreateModal: false,
        openSubmissions: false,
        loadingSubmissions: false,
        submissionsList: [],
        activeAssignmentTitle: '',
        fetchSubmissions(assignId, title) {
            this.activeAssignmentTitle = title;
            this.openSubmissions = true;
            this.loadingSubmissions = true;
            this.submissionsList = [];
            
            fetch(`/teacher/assignments/${assignId}/submissions`)
                .then(res => res.json())
                .then(data => {
                    this.submissionsList = data;
                    this.loadingSubmissions = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loadingSubmissions = false;
                });
        }
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Kelola Tugas & Ujian</h1>
                <p class="text-xs font-bold text-indigo-650">Publikasikan tugas baru, kuis, essay, atau ujian coding untuk kelas binaan Anda</p>
            </div>
            <button type="button" @click="openCreateModal = true"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition-all flex items-center gap-1.5 shrink-0 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tugas
            </button>
        </div>

        <!-- Full Width Assignments List Grouped by Mapel -->
        <div class="space-y-6" x-data="{ activeMapel: '' }">
            <div class="flex flex-wrap gap-3">
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
                                <h3 class="text-base font-black text-slate-800 dark:text-slate-150">Daftar Tugas: {{ $sub->name }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold">Kelas: {{ $sub->classroom->name ?? '-' }}</p>
                            </div>
                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-full font-black">
                                {{ $sub->assignments->count() }} Tugas
                            </span>
                        </div>

                        @if ($sub->assignments->isEmpty())
                            <div class="py-12 text-center text-xs text-slate-400 italic">
                                Belum ada tugas belajar yang dibuat untuk kelas ini.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($sub->assignments as $assign)
                                    <div class="p-5 bg-slate-50/50 dark:bg-slate-850/20 border border-slate-200/50 dark:border-slate-800 rounded-2xl grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                        <div class="md:col-span-8 lg:col-span-9">
                                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                <span class="text-[9px] font-black uppercase px-2 py-0.5 border rounded bg-indigo-50 border-indigo-100 text-indigo-650">
                                                    {{ $assign->type }}
                                                </span>
                                                <span class="text-[10px] text-slate-450 dark:text-slate-500 font-bold">
                                                    Tenggat: {{ \Carbon\Carbon::parse($assign->deadline)->translatedFormat('d M Y, H:i') }} WIB
                                                </span>
                                            </div>
                                            <h4 class="text-xs font-black text-slate-805 dark:text-slate-200 mb-1.5">{{ $assign->title }}</h4>
                                            <p class="text-[11px] text-slate-550 dark:text-slate-400 font-medium leading-relaxed">
                                                {{ $assign->description }}
                                            </p>
                                        </div>

                                        <div class="md:col-span-4 lg:col-span-3 flex flex-wrap items-center gap-3 justify-start md:justify-end">
                                            @if ($assign->file_path)
                                                <a href="{{ asset('storage/' . $assign->file_path) }}" target="_blank"
                                                   class="text-[9px] font-black text-indigo-650 hover:text-indigo-850 hover:underline flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Buka Lampiran Soal
                                                </a>
                                            @endif

                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('teacher.grades.index', ['assignment_id' => $assign->id]) }}"
                                                   class="text-[10px] font-black bg-emerald-50 hover:bg-emerald-100 text-emerald-600 px-3.5 py-1.5 rounded-xl transition-all">
                                                    Beri Nilai
                                                </a>
                                                <button type="button" 
                                                        @click="fetchSubmissions({{ $assign->id }}, '{{ addslashes($assign->title) }}')"
                                                        class="text-[10px] font-black bg-indigo-50 hover:bg-indigo-100 text-indigo-650 px-3.5 py-1.5 rounded-xl transition-all">
                                                    Lihat Jawaban
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- MODAL ALPINEJS: BUAT TUGAS BARU -->
        <div x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="openCreateModal = false"
                 x-show="openCreateModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-lg overflow-hidden p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-800">Buat Tugas Baru</h3>
                    <button type="button" @click="openCreateModal = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>

                <form method="POST" action="{{ route('teacher.assignments.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Pilih Mapel & Kelas -->
                    <div>
                        <label for="subject_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mata Pelajaran (Kelas):</label>
                        <select name="subject_id" id="subject_id" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500 font-bold">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach ($mySubjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} (Kelas {{ $sub->classroom->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Judul Tugas -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Tugas:</label>
                        <input type="text" name="title" id="title" required placeholder="Tulis judul tugas..."
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tipe Tugas -->
                        <div>
                            <label for="type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Pengerjaan:</label>
                            <select name="type" id="type" required
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500 font-bold">
                                <option value="essay">Jawaban Teks (Essay)</option>
                                <option value="file">Unggah Berkas (File Upload)</option>
                                <option value="quiz">Kuis Pilihan Ganda / Isian</option>
                                <option value="coding">Ujian Pemrograman (Coding)</option>
                                <option value="project">Tugas Proyek / Praktek</option>
                            </select>
                        </div>

                        <!-- Batas Waktu / Deadline -->
                        <div>
                            <label for="deadline" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tenggat Waktu:</label>
                            <input type="datetime-local" name="deadline" id="deadline" required
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Deskripsi / Petunjuk -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Instruksi Tugas:</label>
                        <textarea name="description" id="description" rows="4" required placeholder="Tulis rincian instruksi tugas bagi siswa..."
                                  class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Berkas Penunjang Tugas -->
                    <div>
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lampiran Soal / File Penunjang:</label>
                        <input type="file" name="file" id="file"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-500 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                        <p class="text-[9px] text-slate-400 mt-1 font-medium">Format: PDF, Word, Zip, Gambar. Maks 10MB.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openCreateModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black transition-all shadow-md">
                            Publikasikan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL JAWABAN SISWA (AlpineJS) -->
        <div x-show="openSubmissions" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="openSubmissions = false"
                 x-show="openSubmissions"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-4xl overflow-hidden p-6 space-y-4">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-black text-slate-800">Detail Hasil Jawaban Siswa</h3>
                        <p class="text-xs font-bold text-indigo-650" x-text="'Tugas: ' + activeAssignmentTitle"></p>
                    </div>
                    <button type="button" @click="openSubmissions = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>

                <!-- Loader -->
                <div x-show="loadingSubmissions" class="py-12 flex flex-col items-center justify-center gap-3">
                    <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                    <span class="text-xs font-bold text-slate-400">Sedang memuat data jawaban...</span>
                </div>

                <!-- Content Table -->
                <div x-show="!loadingSubmissions" class="space-y-4">
                    <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-96 overflow-y-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-450 uppercase text-[9px] font-bold tracking-wider">
                                    <th class="px-4 py-2.5 w-10 text-center">No</th>
                                    <th class="px-4 py-2.5 min-w-[150px]">Nama Siswa</th>
                                    <th class="px-4 py-2.5 w-36 text-center">Status</th>
                                    <th class="px-4 py-2.5 min-w-[200px]">Isi Jawaban</th>
                                    <th class="px-4 py-2.5 text-center w-20">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-bold">
                                <template x-for="(sub, idx) in submissionsList" :key="idx">
                                    <tr>
                                        <td class="px-4 py-3 text-center text-slate-400" x-text="idx + 1"></td>
                                        <td class="px-4 py-3">
                                            <div class="text-slate-800" x-text="sub.student_name"></div>
                                            <div class="text-[9px] text-slate-400 font-normal mt-0.5" x-text="sub.student_email"></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <template x-if="sub.status === 'submitted'">
                                                <div class="flex flex-col gap-0.5 items-center">
                                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded text-[8px] uppercase font-black">
                                                        Terkumpul
                                                    </span>
                                                    <span class="text-[8px] text-slate-400 font-normal mt-0.5" x-text="sub.submitted_at"></span>
                                                </div>
                                            </template>
                                            <template x-if="sub.status === 'none'">
                                                <span class="bg-rose-50 text-rose-500 border border-rose-100 px-2 py-0.5 rounded text-[8px] uppercase font-black">
                                                    Belum Kirim
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-3 font-normal text-slate-600 leading-relaxed">
                                            <div class="space-y-1.5">
                                                <template x-if="sub.content">
                                                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100 max-h-24 overflow-y-auto text-[10px] font-medium" x-text="sub.content"></div>
                                                </template>
                                                <template x-if="sub.file_url">
                                                    <a :href="sub.file_url" target="_blank"
                                                       class="text-[9px] font-black text-indigo-600 hover:underline flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        Unduh Dokumen Siswa
                                                    </a>
                                                </template>
                                                <template x-if="!sub.content && !sub.file_url">
                                                    <span class="text-[10px] text-slate-400 italic">Tidak ada berkas/jawaban</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <template x-if="sub.score !== null">
                                                <span class="text-sm font-black text-indigo-650" x-text="sub.score"></span>
                                            </template>
                                            <template x-if="sub.score === null">
                                                <span class="text-xs text-slate-400 font-medium">--</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Button -->
                    <div class="flex justify-end pt-3">
                        <button type="button" @click="openSubmissions = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

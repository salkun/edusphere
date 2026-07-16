<x-app-layout>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 dark:text-slate-100">Kelola Pengumuman</h1>
        <p class="text-xs font-bold text-indigo-650">Kirim informasi, berita, dan pengumuman kepada siswa kelas yang Anda ajar</p>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Form Buat Pengumuman Baru -->
        <div class="lg:col-span-1">
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                    Buat Pengumuman Baru
                </h2>

                <form method="POST" action="{{ route('teacher.announcements.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Pilih Kelas Target -->
                    <div>
                        <label for="class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kelas Penerima:</label>
                        <select name="class_id" id="class_id" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $cr)
                                <option value="{{ $cr->id }}">{{ $cr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Judul Pengumuman -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Pengumuman:</label>
                        <input type="text" name="title" id="title" required placeholder="Tulis judul info..."
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Isi Informasi -->
                    <div>
                        <label for="content" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konten / Isi Informasi:</label>
                        <textarea name="content" id="content" rows="6" required placeholder="Tulis pesan pengumuman Anda di sini..."
                                  class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-700 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Unggah Gambar Opsional -->
                    <div>
                        <label for="image" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Gambar Lampiran (Opsional):</label>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-500 bg-white shadow-sm focus:ring-1 focus:ring-indigo-500">
                        <p class="text-[9px] text-slate-400 mt-1 font-medium">Format: PNG, JPG, JPEG. Maks 5MB.</p>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl py-3 text-xs font-black transition-all shadow-md">
                        Kirim Pengumuman
                    </button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Riwayat Pengumuman Terkirim -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm">
                <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-emerald-500 rounded-full"></span>
                    Riwayat Pengumuman Terkirim
                </h2>

                @if ($announcements->isEmpty())
                    <div class="py-12 text-center text-xs text-slate-400 italic">
                        Belub ada pengumuman yang Anda buat.
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($announcements as $ann)
                            <div class="p-5 bg-slate-50/50 dark:bg-slate-850/30 border border-slate-200/60 dark:border-slate-800 rounded-2xl flex flex-col md:flex-row gap-5">
                                
                                @if ($ann->image_path)
                                    <div class="w-full md:w-40 h-28 rounded-xl overflow-hidden shrink-0 border border-slate-100 bg-white">
                                        <img src="{{ asset('storage/' . $ann->image_path) }}" alt="Lampiran" class="w-full h-full object-cover">
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="text-[9px] font-black uppercase text-indigo-650 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded">
                                                Kelas: {{ $ann->classroom->name ?? '-' }}
                                            </span>
                                            <span class="text-[9px] text-slate-400 font-bold">
                                                {{ $ann->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-black text-slate-800 dark:text-slate-150 mb-1.5">{{ $ann->title }}</h3>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium whitespace-pre-line leading-relaxed">{{ $ann->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

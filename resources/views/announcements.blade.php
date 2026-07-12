<x-app-layout>
    <!-- Header Card Pengumuman -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Informasi & Berita</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Pengumuman Kelas - {{ $classroom->name ?? '-' }}</h1>
            <p class="text-slate-500 font-medium">Temukan seluruh pengumuman resmi, jadwal penting, dan berita kelas dari Wali Kelas Anda.</p>
        </div>
        
        <!-- Wali Kelas Card -->
        <div class="flex items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-100 shrink-0">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                {{ $classroom && $classroom->homeroomTeacher ? substr($classroom->homeroomTeacher->name, 3, 1) : 'W' }}
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold tracking-wider uppercase">WALI KELAS</div>
                <div class="text-base font-bold text-slate-800">{{ $classroom->homeroomTeacher->name ?? 'Belum Ditentukan' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Penanggung Jawab Kelas</div>
            </div>
        </div>
    </div>

    <!-- Announcement Feed -->
    <div class="space-y-8 max-w-4xl mx-auto">
        @if($announcements->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200/60 text-slate-500">
                Belum ada pengumuman yang dibagikan untuk kelas Anda.
            </div>
        @else
            @foreach($announcements as $announcement)
                <article class="bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-200">
                    <!-- Announcement Image Banner -->
                    @if($announcement->image_path)
                        <div class="aspect-[21/9] w-full relative overflow-hidden bg-slate-100 border-b border-slate-100">
                            <img src="{{ asset($announcement->image_path) }}" alt="{{ $announcement->title }}" class="object-cover w-full h-full hover:scale-102 transition-transform duration-500" />
                        </div>
                    @endif

                    <!-- Announcement Content -->
                    <div class="p-8">
                        <div class="flex items-center gap-3 text-xs text-slate-400 font-semibold mb-3">
                            <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md font-bold">Wali Kelas</span>
                            <span>&bull;</span>
                            <span>{{ \Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>

                        <h2 class="text-2xl font-black text-slate-800 mb-4 leading-tight">
                            {{ $announcement->title }}
                        </h2>

                        <div class="prose prose-slate max-w-none text-slate-650 text-sm leading-relaxed whitespace-pre-line space-y-4">
                            {!! nl2br(e($announcement->content)) !!}
                        </div>
                    </div>
                </article>
            @endforeach
        @endif
    </div>
</x-app-layout>

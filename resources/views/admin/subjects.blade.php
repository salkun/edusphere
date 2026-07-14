<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1 font-sans">Kelola Mapel & Jadwal</h1>
            <p class="text-slate-500 font-medium font-sans">Kelola mata pelajaran akademik, tunjuk guru pengampu (bisa lebih dari 1 guru per mapel), serta susun jadwal jam belajarnya pada satu atau beberapa hari sekaligus.</p>
        </div>
    </div>

    <!-- Alert Status -->
    @if (session('success'))
        <div class="mb-6 p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- MAIN BODY -->
    <div x-data="{ open: false, isEdit: false, actionUrl: '', name: '', class_id: '', teacher_ids: [], days: [], start_time: '07:00', end_time: '08:30' }" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-800">Daftar Mata Pelajaran & Jadwal</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium font-sans">Semua mapel dan jadwal yang dikelola di sistem akademik Edusphere.</p>
            </div>
            <button @click="open = true; isEdit = false; actionUrl = '{{ route('admin.subjects.store') }}'; name = ''; class_id = ''; teacher_ids = []; days = []; start_time = '07:00'; end_time = '08:30';" 
                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Mapel & Jadwal
            </button>
        </div>

        <div class="overflow-x-auto -mx-8">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <th class="px-8 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Guru Pengampu</th>
                        <th class="px-6 py-4">Jadwal (Hari, Jam & Durasi)</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                    @forelse ($subjects as $s)
                        <tr>
                            <td class="px-8 py-4 font-black text-slate-800">
                                {{ $s->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-indigo-50 text-indigo-600 text-xs font-bold">
                                    {{ $s->classroom->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @forelse ($s->teachers as $teacher)
                                    <div class="flex items-center gap-1.5 mb-1 last:mb-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="font-bold text-slate-800">{{ $teacher->name }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-400 italic font-medium">Belum ada Guru Pengampu</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-1.5 text-slate-650">
                                    @foreach(explode(',', $s->day) as $dayItem)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-xs font-bold text-slate-500">{{ $dayItem }}</span>
                                    @endforeach
                                    <span class="text-xs font-mono font-bold ml-1">{{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}</span>
                                    <span class="text-xs text-indigo-600 bg-indigo-50/50 px-1.5 py-0.5 rounded-full font-bold ml-1">
                                        {{ \Carbon\Carbon::parse($s->end_time)->diffInMinutes(\Carbon\Carbon::parse($s->start_time)) }} Menit
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="open = true; isEdit = true; actionUrl = '{{ route('admin.subjects.update', $s->id) }}'; name = '{{ $s->name }}'; class_id = '{{ $s->class_id }}'; teacher_ids = [{{ implode(',', $s->teachers->pluck('id')->toArray()) }}]; days = '{{ $s->day }}'.split(','); start_time = '{{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}'; end_time = '{{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}';" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-xl">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.subjects.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 rounded-xl">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 font-bold text-slate-400 italic">Belum ada mata pelajaran terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL ALPINEJS: KELOLA MAPEL -->
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="open = false" 
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-800" x-text="isEdit ? 'Ubah Mapel & Jadwal' : 'Tambah Mapel & Jadwal'"></h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Nama Mata Pelajaran</label>
                        <input type="text" name="name" x-model="name" required 
                               placeholder="Contoh: Pemrograman Mobil"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Kelas</label>
                        <select name="class_id" x-model="class_id" required 
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                            <option value="" disabled>-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Guru Pengampu (Bisa lebih dari 1)</label>
                        <div class="border border-slate-200 rounded-xl p-3 max-h-36 overflow-y-auto space-y-2 bg-white">
                            @foreach ($teachers as $t)
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="teacher_ids[]" value="{{ $t->id }}" x-model="teacher_ids"
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                    <span>{{ $t->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Hari Belajar (Bisa lebih dari 1)</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $dayItem)
                                <label class="flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 cursor-pointer transition-all">
                                    <input type="checkbox" name="days[]" value="{{ $dayItem }}" x-model="days"
                                           class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-3.5 h-3.5">
                                    <span>{{ $dayItem }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Jam Mulai</label>
                            <input type="text" name="start_time" x-model="start_time" required placeholder="07:00"
                                   class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Jam Selesai</label>
                            <input type="text" name="end_time" x-model="end_time" required placeholder="08:30"
                                   class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition-all">Simpan Mapel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

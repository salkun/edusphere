<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1 font-sans">Kelola Kelas & Wali Kelas</h1>
            <p class="text-slate-500 font-medium font-sans">Daftarkan kelas belajar baru, edit nama kelas, dan tetapkan Wali Kelas untuk masing-masing kelas.</p>
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
    <div x-data="{ open: false, isEdit: false, actionUrl: '', name: '', homeroom_teacher_id: '' }" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-800">Daftar Kelas Belajar</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium font-sans">Semua kelas yang terdaftar dalam sistem akademik Edusphere.</p>
            </div>
            <button @click="open = true; isEdit = false; actionUrl = '{{ route('admin.classes.store') }}'; name = ''; homeroom_teacher_id = '';" 
                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kelas
            </button>
        </div>

        <div class="overflow-x-auto -mx-8">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <th class="px-8 py-4">Nama Kelas</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4 text-center">Jumlah Mapel</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                    @forelse ($classes as $c)
                        <tr>
                            <td class="px-8 py-4 font-black text-slate-800 text-base">
                                {{ $c->name }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($c->homeroomTeacher)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-bold text-slate-800">{{ $c->homeroomTeacher->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic font-medium">Belum ada Wali Kelas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-indigo-600">
                                {{ $c->subjects->count() }} Mapel
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="open = true; isEdit = true; actionUrl = '{{ route('admin.classes.update', $c->id) }}'; name = '{{ $c->name }}'; homeroom_teacher_id = '{{ $c->homeroom_teacher_id ?? '' }}';" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-xl">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Semua mapel dan data murid di kelas ini akan terhapus.')">
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
                            <td colspan="4" class="text-center py-10 font-bold text-slate-400 italic">Belum ada kelas terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL ALPINEJS: KELOLA KELAS -->
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="open = false" class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-md overflow-hidden transform transition-all p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-800" x-text="isEdit ? 'Ubah Kelas' : 'Tambah Kelas'"></h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Nama Kelas</label>
                        <input type="text" name="name" x-model="name" required 
                               placeholder="Contoh: X RPL A"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Wali Kelas</label>
                        <select name="homeroom_teacher_id" x-model="homeroom_teacher_id" 
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                            <option value="">-- Tanpa Wali Kelas --</option>
                            @foreach ($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition-all">Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

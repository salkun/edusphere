<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1 font-sans">Kelola Pengguna</h1>
            <p class="text-slate-500 font-medium font-sans">Tambahkan data siswa, guru, maupun admin baru serta kelola informasi akun pengguna Edusphere.</p>
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
    <div x-data="{ open: false, openImport: false, isEdit: false, actionUrl: '', name: '', email: '', role: 'student', password: '' }" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-800">Daftar Akun Pengguna</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium font-sans">Semua pengguna yang terdaftar di Edusphere.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <!-- Form Pencarian -->
                <form action="{{ route('admin.users.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, role..." 
                           class="w-full sm:w-64 border border-slate-200 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-medium text-slate-805 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-slate-100/50 transition-all">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if (request('search'))
                        <a href="{{ route('admin.users.index') }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-base font-bold">&times;</a>
                    @endif
                </form>

                <!-- Tombol Import CSV/Excel -->
                <button @click="openImport = true" 
                        class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-5 py-3 rounded-2xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import CSV/Excel
                </button>

                <button @click="open = true; isEdit = false; actionUrl = '{{ route('admin.users.store') }}'; name = ''; email = ''; role = 'student'; password = '';" 
                        class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </button>
            </div>
        </div>

        <!-- Tab Penyaring Peran -->
        <div class="flex border-b border-slate-100 gap-6 mb-6">
            <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'role'), [])) }}" 
               class="pb-3 border-b-2 text-xs font-bold transition-all {{ !request()->has('role') ? 'border-indigo-650 text-indigo-650 font-black' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Semua Pengguna
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'student'])) }}" 
               class="pb-3 border-b-2 text-xs font-bold transition-all {{ request('role') === 'student' ? 'border-indigo-650 text-indigo-650 font-black' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Siswa
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'teacher'])) }}" 
               class="pb-3 border-b-2 text-xs font-bold transition-all {{ request('role') === 'teacher' ? 'border-indigo-650 text-indigo-650 font-black' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Guru
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'admin'])) }}" 
               class="pb-3 border-b-2 text-xs font-bold transition-all {{ request('role') === 'admin' ? 'border-indigo-650 text-indigo-650 font-black' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Admin
            </a>
        </div>

        <div class="overflow-x-auto -mx-8">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <th class="px-8 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Alamat Email</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                    @forelse ($users as $u)
                        <tr>
                            <td class="px-8 py-4 font-black text-slate-800 text-base">
                                {{ $u->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($u->role === 'admin')
                                    <span class="text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full uppercase tracking-wider">Admin</span>
                                @elseif ($u->role === 'teacher')
                                    <span class="text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full uppercase tracking-wider">Guru</span>
                                @else
                                    <span class="text-[10px] font-bold bg-violet-50 text-violet-600 px-2.5 py-1 rounded-full uppercase tracking-wider">Siswa</span>
                                @endif
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="open = true; isEdit = true; actionUrl = '{{ route('admin.users.update', $u->id) }}'; name = '{{ $u->name }}'; email = '{{ $u->email }}'; role = '{{ $u->role }}'; password = '';" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-xl">
                                        Edit
                                    </button>
                                    @if ($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait (misal rapor, nilai, tugas) akan terhapus permanen.')">
                                            @csrf
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 rounded-xl">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic px-2.5 py-1.5">Akun Anda</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 font-bold text-slate-400 italic">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>

        <!-- MODAL ALPINEJS: KELOLA PENGGUNA -->
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
                    <h3 class="text-lg font-black text-slate-800" x-text="isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna'"></h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" x-model="name" required 
                               placeholder="Nama Lengkap Pengguna"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Alamat Email</label>
                        <input type="email" name="email" x-model="email" required 
                               placeholder="Contoh: user@almuhajirin.sch.id"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Peran (Role)</label>
                            <select name="role" x-model="role" required 
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                                <option value="student">Siswa (Student)</option>
                                <option value="teacher">Guru (Teacher)</option>
                                <option value="admin">Admin (Administrator)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">
                                Password
                                <span x-show="isEdit" class="text-[10px] text-slate-400 lowercase font-medium">(kosongkan jika tidak diubah)</span>
                            </label>
                            <input type="password" name="password" x-model="password" :required="!isEdit"
                                   placeholder="Min. 8 karakter"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition-all">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ALPINEJS: IMPORT CSV -->
        <div x-show="openImport" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="openImport = false" 
                 x-show="openImport"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-800">Import Pengguna dari CSV / Excel</h3>
                    <button type="button" @click="openImport = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Download Template</label>
                        <a href="{{ route('admin.users.template') }}" class="inline-flex items-center gap-2 text-indigo-650 hover:text-indigo-800 text-xs font-bold bg-indigo-50 px-4 py-2.5 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh Template CSV (Excel)
                        </a>
                        <p class="text-[10px] text-slate-400 mt-1 font-medium font-sans">Gunakan file ini sebagai format dasar pengisian data siswa/guru.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Pilih File CSV</label>
                        <input type="file" name="file" required accept=".csv,.txt"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                        <p class="text-[10px] text-slate-400 mt-1 font-medium font-sans">Mendukung format pemisah koma (,) atau titik koma (;) dari Excel.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openImport = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition-all">Unggah & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <!-- Header Card Profil -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Akun Saya</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Profil Siswa</h1>
            <p class="text-slate-500 font-medium">Lihat informasi data diri lengkap Anda dan kelola detail keamanan akun.</p>
        </div>
    </div>

    <div class="space-y-8 max-w-6xl mx-auto">

        @if (session('status') === 'avatar-updated')
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-2" role="alert">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold">Foto profil berhasil diperbarui!</span>
            </div>
        @endif

        <!-- KARTU BIODATA LENGKAP & FOTO PROFIL -->
        <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
            <h2 class="text-lg font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                Data Diri Siswa
            </h2>

            <div class="flex flex-col lg:flex-row gap-10 items-start">
                <!-- Foto Profil (Avatar Section dengan Upload) -->
                <div class="flex flex-col items-center text-center shrink-0 w-full lg:w-64 bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                    @if ($reportCard && $reportCard->avatar_path && file_exists(public_path($reportCard->avatar_path)))
                        <img src="{{ asset($reportCard->avatar_path) }}" class="w-32 h-32 rounded-full object-cover shadow-md mb-4 border-2 border-white ring-4 ring-indigo-50">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-tr from-indigo-500 to-indigo-600 text-white font-black text-4xl flex items-center justify-center shadow-md mb-4 relative overflow-hidden">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <h3 class="text-lg font-black text-slate-800">{{ $user->name }}</h3>
                    <p class="text-xs font-bold uppercase tracking-wider mt-0.5 text-slate-400">Siswa</p>

                    <!-- Form Unggah Foto Profil -->
                    <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="mt-4">
                        @csrf
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors rounded-xl">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Ubah Foto
                            <input type="file" name="avatar" class="hidden" accept=".jpg,.jpeg,.png" onchange="document.getElementById('avatarForm').submit()">
                        </label>
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </form>
                </div>

                <!-- Detail Biodata List -->
                <div class="flex-1 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="space-y-4">
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</div>
                                <div class="text-sm font-black text-slate-800">{{ $user->name }}</div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIS / NISN</div>
                                <div class="text-sm font-mono font-black text-slate-800">{{ $reportCard->nis ?? '-' }} / {{ $reportCard->nisn ?? '-' }}</div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</div>
                                <div class="text-sm font-black text-slate-800">
                                    {{ $reportCard->place_of_birth ?? '-' }}, 
                                    {{ $reportCard->date_of_birth ? \Carbon\Carbon::parse($reportCard->date_of_birth)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</div>
                                <div class="text-sm font-black text-slate-800">{{ $reportCard->gender ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Agama</div>
                                <div class="text-sm font-black text-slate-800">{{ $reportCard->religion ?? '-' }}</div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas Aktif</div>
                                <div class="text-sm font-black text-slate-800">{{ $classroom->name ?? 'Belum Ditentukan' }}</div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Telepon</div>
                                <div class="text-sm font-black text-slate-800">{{ $reportCard->phone_number ?? '-' }}</div>
                            </div>
                            <div class="pb-3 border-b border-slate-50">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Email</div>
                                <div class="text-sm font-black text-slate-800">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Tinggal</div>
                        <div class="text-sm font-black text-slate-800 leading-relaxed">{{ $reportCard->address ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SETTING EDIT SATU KARTU (GANTI EMAIL & PASSWORD) -->
        <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
            <h2 class="text-lg font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                Pengaturan Akun & Keamanan
            </h2>
            
            <div class="space-y-10">
                <!-- Row 1: Ganti Email -->
                <div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <hr class="border-slate-100">

                <!-- Row 2: Ganti Password -->
                <div>
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

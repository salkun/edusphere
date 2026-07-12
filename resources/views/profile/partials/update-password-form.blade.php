<section>
    <header>
        <h2 class="text-lg font-black text-slate-800">
            Perbarui Kata Sandi
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Pastikan akun Anda menggunakan kata sandi yang kuat untuk menjaga keamanan akun.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" class="font-bold text-slate-700" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata Sandi Baru" class="font-bold text-slate-700" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" class="font-bold text-slate-700" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:scale-95 transition-all duration-150 rounded-xl font-bold py-2.5 px-5">Simpan Sandi Baru</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-550 font-bold"
                >Berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>

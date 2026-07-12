<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" class="block mt-1 w-full border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-800 dark:text-slate-250" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@sekolah.sch.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi" class="font-bold text-slate-700 dark:text-slate-300" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-800 dark:text-slate-250"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan kata sandi" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-800 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs font-bold text-slate-500 dark:text-slate-400">Ingat Saya</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-xl active:scale-[0.98] transition-all duration-150 shadow-md shadow-indigo-200 dark:shadow-none">
                Masuk ke Akun
            </button>
        </div>
    </form>
</x-guest-layout>

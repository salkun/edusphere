<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Edusphere') }} - Login</title>

        <!-- Google Fonts: Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Theme & Accent Loader -->
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || 'light';
                const accent = localStorage.getItem('accent') || 'indigo';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                if (accent !== 'indigo') {
                    document.documentElement.classList.add('accent-' + accent);
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 antialiased flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-3xl shadow-xl p-8 sm:p-10 transition-all duration-300">
            <!-- Brand Logo & Header -->
            <div class="mb-8 flex flex-col items-center">
                <a href="/">
                    <img src="{{ asset('logo.avif') }}" alt="Edusphere Logo" class="h-14 w-auto transition-transform hover:scale-105 duration-200">
                </a>
                <h2 class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-4 text-center">Edusphere</h2>
                <p class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider mt-2">Portal Akademik Siswa</p>
            </div>

            <!-- Main Slot -->
            {{ $slot }}
        </div>
    </body>
</html>

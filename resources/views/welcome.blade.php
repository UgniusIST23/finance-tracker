<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finansų apskaitos sistema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        const userPrefersDark = localStorage.getItem('theme') === 'dark' ||
                               (localStorage.getItem('theme') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (userPrefersDark) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="antialiased bg-white dark:bg-neutral-900 text-gray-900 dark:text-white">

    <main class="flex flex-col items-center justify-center px-6 py-20 text-center min-h-screen">
        <h1 class="text-5xl font-extrabold tracking-tight mb-6">
            <span class="text-red-600">Sek</span> savo <span class="text-red-600">finansus</span> efektyviai<span class="text-red-600">!</span>
        </h1>
        <p class="text-lg mb-8 max-w-2xl">
            Tvarkyk pajamas ir išlaidas, analizuok ataskaitas, stebėk balansą ir pasiek savo finansinius tikslus!
        </p>
        <div class="flex gap-4">
            <a href="{{ route('login') }}" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Prisijungti</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="px-6 py-2 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition">Registruotis</a>
            @endif
        </div>
    </main>

    <div class="fixed bottom-4 right-4 z-50">
        <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow-lg transition-colors duration-300 ease-in-out">
            <svg class="w-6 h-6 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M3 12H2m15.325-4.275l.707-.707M6.707 17.275l-.707.707M17.275 6.707l.707-.707M6.707 6.707l-.707-.707M12 6.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11z"></path>
            </svg>
            <svg class="w-6 h-6 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
        </button>
    </div>
</body>
</html>

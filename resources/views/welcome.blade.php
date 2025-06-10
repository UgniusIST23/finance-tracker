<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finansų apskaitos sistema</title>
    @vite('resources/css/app.css')
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

</body>
</html>

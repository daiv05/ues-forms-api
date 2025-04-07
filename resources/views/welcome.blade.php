<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Bienvenido</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="flex flex-col h-screen bg-white">
        <header class="relative z-10 bg-orange-900 text-white p-4">
            <nav class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="{{ Vite::asset('resources/img/ues-logo.png') }}" alt="ReportFIA logo" class="h-10 w-fit">
                    <span class="font-bold text-xl">{{ config('app.name') }}</span>
                </div>
            </nav>
        </header>

        <main class="flex-grow">
            <div class="container mx-auto px-4 py-8">
                <h1 class="text-4xl font-bold text-center text-orange-900 mb-6">Bienvenido a la API de {{ config('app.name') }}</h1>
                <p class="text-lg text-center text-gray-700 mb-4">La plataforma de encuestas de la Universidad de El Salvador</p>
                <div class="flex justify-center">
                    <img src="{{ Vite::asset('resources/img/ues-logo.png') }}" alt="ReportFIA logo" class="h-32 w-fit">
                </div>
            </div>
        </main>

        <footer class="bg-gray-100 py-2">
            <div class="mx-auto w-full max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
                <div class="flex items-center mb-2 ">
                    <img src="{{ Vite::asset('resources/img/ues-logo.png') }}" alt="logo" class="h-10 w-fit mr-2">
                    <span class="font-bold text-xl text-orange-900 mr-4">{{ config('app.name') }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 sm:text-center">
                        © {{ date('Y') }}
                        <a href="{{ config('app.url') }}" class="hover:underline">{{ config('app.name') }}</a>
                        . Todos los derechos reservados.
                    </span>
                </div>
                <ul
                    class="mt-3 flex flex-wrap items-center text-sm font-medium dark:text-gray-400 sm:mt-0 text-orange-900"
                >
                    <li>
                        <a href="#" class="me-4 hover:underline md:me-6">FIA - UES</a>
                    </li>
                    <li>
                        <a href="#" class="me-4 hover:underline md:me-6">Universidad</a>
                    </li>
                    <li>
                        <a href="#" class="me-4 hover:underline md:me-6">Eel</a>
                    </li>
                    <li>
                        <a href="#" class="hover:underline">Contacto</a>
                    </li>
                </ul>
            </div>
        </footer>
    </body>
</html>

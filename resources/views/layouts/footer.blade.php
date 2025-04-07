<footer>
    <div class="mx-auto w-full md:px-4 md:flex md:items-center justify-center md:justify-between">
        <div class="flex items-center justify-center mb-2">
            <img src="{{ Vite::asset('resources/img/ues-logo.png') }}" alt="logo" class="h-10 w-fit mr-2">
            <span class="font-bold text-xl text-orange-900 mr-4">{{ config('app.name') }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400 sm:text-center">
                © 2024
                <a href="{{ config('app.url') }}" class="hover:underline">{{ config('app.name') }}</a>
                . Todos los derechos reservados.
            </span>
        </div>
        <ul
            class="mt-3 flex flex-wrap items-center justify-center text-sm font-medium dark:text-gray-400 sm:mt-0 text-orange-900">
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

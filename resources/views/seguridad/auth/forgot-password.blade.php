<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer la contraseña y elegir una nueva.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-forms.input-label for="email" :value="__('Correo electrónico')" />
            <x-forms.text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-forms.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-forms.primary-button>
                {{ __('Enviar enlace para reestablecer') }}
            </x-forms.primary-button>
        </div>
        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
            href="{{ route('landing') }}">
            <x-heroicon-s-arrow-left class="h-4 w-4" />
        </a>
    </form>
</x-guest-layout>

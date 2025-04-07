<x-guest-layout>
    <div class="p-4">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold mb-1">Doble factor de autenticación</h1> <br />
            @if (session('code-send'))
                <p class="text-[15px] text-slate-500">
                    Hemos detectado que has iniciado sesión desde un dispositivo desconocido. Para continuar, 
                    ingresa el código de verificación enviado a tu correo electrónico
                </p>
            @else
                <p class="text-[15px] text-slate-500">Hemos detectado que has iniciado sesión desde un dispositivo desconocido, 
                    por favor verifique su correo electrónico</p>
            @endif
        </div>
        @if (!session('code-send'))
            <form class="mx-auto flex items-center justify-center" id="otp-form" method="POST"
                action="{{ route('two-factor.reenviar') }}">
                @csrf
                <div class="max-w-[260px] mx-auto mt-4">
                    <button type="submit"
                        class="w-full inline-flex justify-center whitespace-nowrap rounded-lg bg-orange-700 px-3.5 py-2.5 text-sm font-medium text-white shadow-sm shadow-red-950/10 hover:bg-orange-800 focus:outline-none focus:ring focus:ring-red-300 focus-visible:outline-none focus-visible:ring focus-visible:ring-red-300 transition-colors duration-150">Enviar
                        código</button>
                </div>
            </form>
        @else
            <form id="otp-form" method="POST" action="{{ route('two-factor.confirmar') }}"
                onsubmit="verificarCode(event)">
                @csrf
                <input id="code" type="hidden" name="code" value="" />
                <div class="flex items-center justify-center gap-3">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text"
                            class="w-8 md:w-14 h-8 md:h-14 text-center text-lg md:text-2xl font-bold md:font-extrabold text-slate-700 bg-slate-100 border border-transparent hover:border-slate-200 appearance-none rounded p-2 md:p-4 outline-none focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100"
                            pattern="\d*" maxlength="1" />
                    @endfor
                </div>
                <div class="max-w-[260px] mx-auto mt-4">
                    <button type="submit"
                        class="w-full inline-flex justify-center whitespace-nowrap rounded-lg bg-orange-700 px-3.5 py-2.5 text-sm font-medium text-white shadow-sm shadow-red-950/10 hover:bg-orange-800 focus:outline-none focus:ring focus:ring-red-300 focus-visible:outline-none focus-visible:ring focus-visible:ring-red-300 transition-colors duration-150">Verificar</button>
                </div>
            </form>
            <form class="mx-auto flex items-center justify-center" id="otp-form" method="POST"
                action="{{ route('two-factor.reenviar') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-500 mt-4"><span
                        class="font-medium text-orange-700 hover:text-orange-800">Reenviar
                        código</span>
                </button>
            </form>
        @endif
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
            Salir
        </button>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const form = document.getElementById('otp-form')
        const inputs = [...form.querySelectorAll('input[type=text]')]
        const submit = form.querySelector('button[type=submit]')

        const handleKeyDown = (e) => {
            if (
                !/^[0-9]{1}$/.test(e.key) &&
                e.key !== 'Backspace' &&
                e.key !== 'Delete' &&
                e.key !== 'Tab' &&
                !e.metaKey
            ) {
                e.preventDefault()
            }

            if (e.key === 'Delete' || e.key === 'Backspace') {
                const index = inputs.indexOf(e.target);
                if (index > 0) {
                    if (inputs[index].value) {
                        inputs[index].value = '';
                    } else {
                        inputs[index - 1].value = '';
                        inputs[index - 1].focus();
                    }
                }
            }
        }

        const handleInput = (e) => {
            const {
                target
            } = e
            const index = inputs.indexOf(target)
            if (target.value) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus()
                } else {
                    submit.focus()
                }
            }
        }

        const handleFocus = (e) => {
            e.target.select()
        }

        const handlePaste = (e) => {
            e.preventDefault()
            const text = e.clipboardData.getData('text')
            if (!new RegExp(`^[0-9]{${inputs.length}}$`).test(text)) {
                return
            }
            const digits = text.split('')
            inputs.forEach((input, index) => input.value = digits[index])
            submit.focus()
        }

        inputs.forEach((input) => {
            input.addEventListener('input', handleInput)
            input.addEventListener('keydown', handleKeyDown)
            input.addEventListener('focus', handleFocus)
            input.addEventListener('paste', handlePaste)
        })
    })

    const verificarCode = (e) => {
        e.preventDefault()
        const form = document.getElementById('otp-form')
        const codeInput = document.getElementById('code')
        const inputs = [...form.querySelectorAll('input[type=text]')]
        let code = ''
        let flag = true
        inputs.forEach((input, index) => {
            if (!input.value) {
                flag = false
            } else {
                code = code.concat(input.value)
            }
        })
        if (flag) {
            codeInput.value = code
            e.target.submit();
        } else {
            noty('Debe ingresar un código válido', 'error')
        }
    }
</script>

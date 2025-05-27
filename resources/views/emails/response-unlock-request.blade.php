<x-emails.email-container>
    <x-emails.email-body>
        <x-emails.email-header
            title="Solicitud de desbloqueo"
            logoSrc="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3JjWq5hKtAVSofTTo72ynt7SlCxi2G6WXmA&s"
        />

        @php
            $appName = config('app.name');
        @endphp

        @if ($approved)
            <p>
                Estimado usuario, tu solicitud de desbloqueo en {{ $appName }} ha sido
                <strong class="color-finalizado">aprobada</strong>.
                Puedes seguir haciendo uso de la aplicación desde el siguiente enlace
            </p>
        @else
            <p>
                Estimado usuario, tu solicitud de desbloqueo en {{ $appName }} ha sido
                <strong class="color-incompleto">denegada</strong>.
            </p>
            <div>
                <span class="font-bold">Razón:</span>
                <p>
                    {{ $reason ?? '-' }}
                </p>
            </div>
        @endif

        <div class="text-xs">
            <p>Si no haz solicitado ningún código puedes ignorar este correo.</p>
        </div>
    </x-emails.email-body>

    <x-emails.email-footer
        :footerLinks="[
            ['label' => 'Página oficial', 'url' => 'https://www.ues.edu.sv'],
            ['label' => 'Contacto', 'url' => 'https://www.ues.edu.sv/contacto/'],
            ['label' => 'Noticias', 'url' => 'https://www.ues.edu.sv/noticias/'],
        ]"
    />
</x-emails.email-container>

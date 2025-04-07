<x-emails.email-container>
    <x-emails.email-body>
        <x-emails.email-header
            title="Código de verificación"
            logoSrc="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3JjWq5hKtAVSofTTo72ynt7SlCxi2G6WXmA&s" />
        <p>
            Hemos detectado que has iniciado sesión desde un dispositivo desconocido.
            Para continuar, ingresa el siguiente código de verificación:
            <strong>{{ $token }}</strong>
        </p>
        <p>
            Si no haz solicitado ningún código puedes ignorar este correo.
        </p>
    </x-emails.email-body>

    <x-emails.email-footer :footerLinks="[
        ['label' => 'Página oficial', 'url' => 'https://www.ues.edu.sv'],
        ['label' => 'Contacto', 'url' => 'https://www.ues.edu.sv/contacto/'],
        ['label' => 'Noticias', 'url' => 'https://www.ues.edu.sv/noticias/'],
    ]" />
</x-emails.email-container>


<x-emails.email-container>
    <x-emails.email-body>
        <x-emails.email-header title="Reestablecer Contraseña"
            logoSrc="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3JjWq5hKtAVSofTTo72ynt7SlCxi2G6WXmA&s" />
        <p>
            Está recibiendo este correo electrónico porque hemos recibido una solicitud de restablecimiento de
            contraseña para su cuenta.
        </p>
        <div class="flex-div">
            <a class="btn-primary" href="{{ $token }}">Reestablecer</a>
        </div>
        <p>
            Si no haz solicitado ningún reestablecimiento puedes ignorar este correo.
        </p>
    </x-emails.email-body>

    <x-emails.email-footer :footerLinks="[
        ['label' => 'Página oficial', 'url' => 'https://www.ues.edu.sv'],
        ['label' => 'Contacto', 'url' => 'https://www.ues.edu.sv/contacto/'],
        ['label' => 'Noticias', 'url' => 'https://www.ues.edu.sv/noticias/'],
    ]" />
</x-emails.email-container>

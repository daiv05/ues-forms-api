@props([
    'content', // El contenido principal, como el mensaje
])

<div class="container">

    {{ $slot }} <!-- Esto es donde se inserta la tabla o más contenido -->
</div>

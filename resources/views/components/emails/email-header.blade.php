@props([
    'logoSrc',  // La fuente de la imagen del logo
    'title' => 'Asignación de Reporte',  // Título predeterminado
])

<div class="header text-center">
    <img src="{{ $logoSrc }}" class="h-36" alt="logo"  />
    <h2 class="color-incompleto">{{ $title }}</h2>
</div>

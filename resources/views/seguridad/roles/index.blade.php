@php
    $headers = [
        ['text' => 'Nombre', 'align' => 'left'],
        ['text' => 'Estado', 'align' => 'center'],
        ['text' => 'Acción', 'align' => 'left'],
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-header.simple titulo="Gestión de Roles" />
        <div class="p-6">
            @canany(['ROLES_CREAR'])
            <x-forms.primary-button class="block" type="button" id="add-button"
                onclick="window.location.href='{{ url('seguridad/roles/create') }}'">
                Añadir
            </x-forms.primary-button>
            @endcanany
        </div>
    </x-slot>
    <x-container>
        <div class="overflow-x-auto mb-8">
            <x-table.base :headers="$headers">
                @foreach ($roles as $rol)
                    <x-table.tr>
                        <x-table.td>
                            {{ $rol->name }}
                        </x-table.td>
                        <x-table.td justify="center">
                            <x-status.is-active :active="$rol->activo" />
                        </x-table.td>
                        <x-table.td>
                            <div class="flex space-x-2">
                                @canany(['ROLES_EDITAR'])
                                <a href="{{ url('seguridad/roles/' . $rol->id . '/edit') }}"
                                    class="edit-button font-medium text-green-600 hover:underline dark:text-green-400">
                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                </a>
                                @endcanany
                                @canany(['ROLES_VER'])
                                <a href="{{ url('seguridad/roles/' . $rol->id) }}"
                                    class="view-button font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    <x-heroicon-o-eye class="h-5 w-5" />
                                </a>
                                @endcanany
                            </div>
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            </x-table.base>
        </div>
        <nav class="flex-column flex flex-wrap items-center justify-center pt-4 md:flex-row"
            aria-label="Table navigation">
            {{ $roles->links() }}
        </nav>
    </x-container>
</x-app-layout>

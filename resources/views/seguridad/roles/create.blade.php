<x-app-layout>
    <x-slot name="header">
        <x-header.simple titulo="Creación de roles" />
    </x-slot>

    <x-container>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <x-forms.row :columns="1">
                <x-forms.field label="Nombre del Rol" name="name" :value="old('name')" :error="$errors->get('name')" required />
            </x-forms.row>

            <x-forms.row :fullRow="true">
                <x-picklist.picklist :items="$permissions" :asignados="[]" tituloDisponibles="Permisos disponibles"
                    tituloAsignados="Permisos asignados" placeholderDisponibles="Buscar permisos..." placeholderAsignados="Buscar permisos asignados..." inputName="permissions" />
            </x-forms.row>

            <div class="flex justify-center mt-6">
                <x-forms.button-group>
                    <x-forms.cancel-button href="{{ route('roles.index') }}">
                        Cancelar
                    </x-forms.cancel-button>

                    <x-forms.primary-button class="ml-3">
                        Guardar Rol
                    </x-forms.primary-button>
                </x-forms.button-group>
            </div>
        </form>
    </x-container>
</x-app-layout>

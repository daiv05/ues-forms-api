<x-app-layout>
    <x-slot name="header">
        <x-header.simple titulo="Gestión de usuarios" />
    </x-slot>

    <x-container>
        <div class="pt-4 pb-8 text-2xl font-bold text-red-900 dark:text-gray-100">
            {{ $user->es_estudiante ? 'Estudiante' : 'Empleado' }} : {{ $user->persona->nombre . ' ' . $user->persona->apellido }}
        </div>
        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <x-forms.row :columns="2">
                <x-forms.field
                    label="Nombre completo"
                    name="nombre"
                    :value="old('nombre', $user->persona->nombre)"
                    type="text"
                    class="bg-gray-100"
                />

                <x-forms.field
                    label="Apellido"
                    name="apellido"
                    :value="old('apellido', $user->persona->apellido)"
                    type="text"
                    class="bg-gray-100"
                />
            </x-forms.row>

            <x-forms.row :columns="2">
                <x-forms.field
                    label="Correo electrónico"
                    name="email"
                    :value="old('email', $user->email)"
                    type="email"
                    class="bg-gray-100"
                />

                <x-forms.field
                    label="Usuario/Carnet"
                    name="carnet"
                    :value="old('carnet', $user->carnet)"
                    type="text"
                    class="bg-gray-100"
                />
            </x-forms.row>
            <x-forms.row :columns="2">
                @if($user->es_estudiante)
                    <x-forms.select label="Escuela" name="escuela" :options="$escuelas" :selected="old('escuela', $user->id_escuela)"
                        :error="$errors->get('escuela')" required />
                @endif
                <div class="flex h-full items-center ml-2">
                    <x-forms.checkbox
                        label="Activo"
                        name="activo"
                        :checked="$user->activo"
                    />
                </div>
            </x-forms.row>
            <x-forms.row :fullRow="true">
                <x-picklist.picklist
                    :items="$roles"
                    :asignados="$user->roles->pluck('id')->toArray()"
                    tituloDisponibles="Roles disponibles"
                    tituloAsignados="Roles asignados"
                    placeholderDisponibles="Buscar roles..."
                    placeholderAsignados="Buscar asignados..."
                    inputName="roles"
                />
            </x-forms.row>
            <div class="flex justify-center">
                <x-forms.button-group>
                    <x-forms.cancel-button href="{{ route('usuarios.index') }}">
                        Cancelar
                    </x-forms.cancel-button>

                    <x-forms.primary-button class="ml-3">
                        Guardar Cambios
                    </x-forms.primary-button>
                </x-forms.button-group>
            </div>

        </form>
    </x-container>
</x-app-layout>

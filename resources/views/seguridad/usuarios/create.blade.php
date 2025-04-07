<x-app-layout>
    <x-slot name="header">
        <x-header.simple titulo="Gestión de usuarios" />
    </x-slot>

    <x-container>
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <x-forms.row :columns="2">
                <x-forms.field label="Nombre" name="nombre" :value="old('nombre')" :error="$errors->get('nombre')"  required/>
                <x-forms.field label="Apellido" name="apellido" :value="old('apellido')" :error="$errors->get('apellido')" required />
            </x-forms.row>
            <x-forms.row :columns="2">
                <div>
                    <x-forms.input-label for="fecha_nacimiento" :value="__('Fecha de nacimiento')" required />
                    <x-forms.date-input name="fecha_nacimiento" :value="old('fecha_nacimiento')" placeholder="Seleccione una fecha" />
                    <x-forms.input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                </div>
                <x-forms.field label="Teléfono" name="telefono" :value="old('telefono')" :error="$errors->get('telefono')" required />
            </x-forms.row>
            <x-forms.row :columns="2">
                <x-forms.field label="Correo electrónico" name="email" type="email" :value="old('email')"
                    :error="$errors->get('email')" required />
                <x-forms.field label="Usuario/Carnet" name="carnet" :value="old('carnet')"
                    :error="$errors->get('carnet')" required />
            </x-forms.row>
            <x-forms.row :columns="2">
                <x-forms.select label="Tipo usuario" id="tipo_user" name="tipo_user" :options="['1' => 'Estudiante', '0' => 'Empleado']"
                    :value="old('tipo_user')" :error="$errors->get('tipo_user')" onchange="toggleFields()" required />
                <div class="flex h-full items-center ml-2">
                    <x-forms.checkbox label="Activo" name="activo" :checked="old('activo', true)" :error="$errors->get('activo')" required />
                </div>
            </x-forms.row>

            <div id="fieldGroupEst" class="dynamic-field">
                <x-forms.row :columns="2">
                    <x-forms.select label="Escuela" name="escuela" :options="$escuelas" :value="old('escuela')"
                        :error="$errors->get('escuela')" required />
                </x-forms.row>
            </div>

            <div id="fieldGroupEmp" class="dynamic-field">
                <x-forms.row :columns="2">
                    <x-forms.select label="Entidad" name="entidad" :options="$entidades" :value="old('entidad')"
                        onchange="filtrarPuestos()" />
                    <x-forms.select label="Puesto" id="puesto" name="puesto" :options="$puestos[old('entidad')] ?? []" :value="old('puesto')"
                        :error="$errors->get('puesto')" required/>
                </x-forms.row>
                <x-forms.row :fullRow="true">
                    <x-picklist.picklist :items="$roles" :asignados="[]" tituloDisponibles="Roles disponibles"
                        tituloAsignados="Roles asignados" placeholderDisponibles="Buscar roles..."
                        placeholderAsignados="Buscar roles asignados..." inputName="roles" />
                </x-forms.row>
            </div>
            <div class="flex justify-center">
                <x-forms.button-group>
                    <x-forms.cancel-button href="{{ route('usuarios.index') }}">
                        Cancelar
                    </x-forms.cancel-button>

                    <x-forms.primary-button class="ml-3" id="guardar">
                        Guardar Cambios
                    </x-forms.primary-button>
                </x-forms.button-group>
            </div>
        </form>
    </x-container>
    <script>
        const puestosPorEntidad = @json($puestos);

        function filtrarPuestos() {
            const entidadId = document.querySelector('[name="entidad"]').value;
            console.log(entidadId);
            const puestoSelect = document.querySelector('[name="puesto"]');

            // Limpiar el campo de puestos
            puestoSelect.innerHTML = '<option value="">Seleccionar Puesto</option>';

            if (entidadId && puestosPorEntidad[entidadId]) {
                // Agregar puestos filtrados al select
                Object.entries(puestosPorEntidad[entidadId]).forEach(([id, nombre]) => {
                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = nombre;
                    puestoSelect.appendChild(option);
                });
            }
        }

        function toggleFields() {
            const selectValue = document.getElementById('tipo_user').value;

            // Oculta todos los campos dinámicos inicialmente
            document.querySelectorAll('.dynamic-field').forEach(field => {
                field.style.display = 'none';
            });

            document.getElementById('guardar').disabled = true;
            document.getElementById('guardar').style.opacity = '0.5';

            // Muestra los campos correspondientes al valor seleccionado
            if (selectValue === '1') {
                document.getElementById('fieldGroupEst').style.display = 'block';
                document.getElementById('guardar').disabled = false;{
                document.getElementById('guardar').style.opacity = '1';
                }
            } else if (selectValue === '0') {
                document.getElementById('fieldGroupEmp').style.display = 'block';
                document.getElementById('guardar').disabled = false;
                document.getElementById('guardar').style.opacity = '1';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleFields();
        });
    </script>
</x-app-layout>

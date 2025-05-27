<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\PermisosEnum;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'ADMINISTRADOR',
                'permisos' => PermisosEnum::cases(),
                'descripcion' => 'Rol con todos los permisos del sistema',
            ],
            [
                'nombre' => 'ENCUESTADOR',
                'permisos' => [
                    PermisosEnum::ENCUESTA_VER->value,
                    PermisosEnum::ENCUESTA_EDITOR->value,
                    PermisosEnum::ENCUESTA_ESTADISTICAS->value,
                    PermisosEnum::ENCUESTA_PUBLICAR->value,
                    PermisosEnum::GRUPO_META_VER->value,
                    PermisosEnum::GRUPO_META_CREAR->value,
                    PermisosEnum::GRUPO_META_ACTUALIZAR->value,
                ],
                'descripcion' => 'Rol para encuestadores',
            ],
        ];

        foreach ($roles as $rol) {
            $role = Role::create([
                'name' => $rol['nombre'],
                'description' => $rol['descripcion'],
                'activo' => true,
                'guard_name' => 'api'
            ]);
            foreach ($rol['permisos'] as $perm) {
                $role->givePermissionTo($perm);
            }
        }
    }
}

<?php

namespace App\Enums;

enum RolesEnum: string
{
    case SUPERADMIN = 'SUPERADMIN';
    case ADMIN_REPORTE = 'ADMINISTRADOR DE REPORTES';
    case SUPERVISOR_REPORTE = 'SUPERVISOR';
    case EMPLEADO = 'EMPLEADO';
    case USUARIO = 'USUARIO';

    public function permisos(): array
    {
        $perm = [];
        $rolePermissions = [
            self::SUPERADMIN->value => array_map(fn($per) => $per->value, PermisosEnum::cases()),
            self::ADMIN_REPORTE->value => [
                PermisosEnum::REPORTES_CREAR->value,
                PermisosEnum::REPORTES_ASIGNAR->value,
                PermisosEnum::REPORTES_ACTUALIZAR_ESTADO->value,
                PermisosEnum::REPORTES_VER_LISTADO_GENERAL->value,
                PermisosEnum::REPORTES_VER_ASIGNACIONES->value,
                PermisosEnum::REPORTES_REVISION_SOLUCION->value,
                PermisosEnum::ACTIVIDADES_CREAR_REPORTE->value,
                PermisosEnum::RECURSOS_VER->value,
                PermisosEnum::RECURSOS_CREAR->value,
                PermisosEnum::RECURSOS_EDITAR->value,
                PermisosEnum::UNIDADES_MEDIDA_VER->value,
                PermisosEnum::UNIDADES_MEDIDA_CREAR->value,
                PermisosEnum::UNIDADES_MEDIDA_EDITAR->value,
                PermisosEnum::CLASES_VER->value,
                PermisosEnum::EVENTOS_VER->value,
            ],
            self::SUPERVISOR_REPORTE->value => [
                PermisosEnum::REPORTES_CREAR->value,
                PermisosEnum::REPORTES_ACTUALIZAR_ESTADO->value,
                PermisosEnum::REPORTES_VER_LISTADO_GENERAL->value,
                PermisosEnum::REPORTES_VER_ASIGNACIONES->value,
                PermisosEnum::REPORTES_REVISION_SOLUCION->value,
                PermisosEnum::ACTIVIDADES_CREAR_REPORTE->value,
                PermisosEnum::RECURSOS_VER->value,
                PermisosEnum::RECURSOS_CREAR->value,
                PermisosEnum::RECURSOS_EDITAR->value,
                PermisosEnum::UNIDADES_MEDIDA_VER->value,
                PermisosEnum::UNIDADES_MEDIDA_CREAR->value,
                PermisosEnum::UNIDADES_MEDIDA_EDITAR->value,
                PermisosEnum::CLASES_VER->value,
                PermisosEnum::EVENTOS_VER->value,
                PermisosEnum::BIENES_VER->value,
                PermisosEnum::BIENES_CREAR->value,
                PermisosEnum::BIENES_EDITAR->value,
                PermisosEnum::TIPOS_BIENES_VER->value,
                PermisosEnum::TIPOS_BIENES_CREAR->value,
                PermisosEnum::TIPOS_BIENES_EDITAR->value,
            ],
            self::EMPLEADO->value => [
                PermisosEnum::REPORTES_CREAR->value,
                PermisosEnum::REPORTES_ACTUALIZAR_ESTADO->value,
                PermisosEnum::REPORTES_VER_LISTADO_GENERAL->value,
                PermisosEnum::REPORTES_VER_ASIGNACIONES->value,
                PermisosEnum::ACTIVIDADES_CREAR_REPORTE->value,
                PermisosEnum::RECURSOS_VER->value,
                PermisosEnum::RECURSOS_CREAR->value,
                PermisosEnum::RECURSOS_EDITAR->value,
                PermisosEnum::UNIDADES_MEDIDA_VER->value,
                PermisosEnum::UNIDADES_MEDIDA_CREAR->value,
                PermisosEnum::UNIDADES_MEDIDA_EDITAR->value,
                PermisosEnum::CLASES_VER->value,
                PermisosEnum::EVENTOS_VER->value,
                PermisosEnum::BIENES_VER->value,
                PermisosEnum::BIENES_CREAR->value,
                PermisosEnum::BIENES_EDITAR->value,
                PermisosEnum::TIPOS_BIENES_VER->value,
                PermisosEnum::TIPOS_BIENES_CREAR->value,
                PermisosEnum::TIPOS_BIENES_EDITAR->value,

            ],
            self::USUARIO->value => [
                PermisosEnum::REPORTES_CREAR->value,
                PermisosEnum::REPORTES_VER_LISTADO_GENERAL->value,
                PermisosEnum::ACTIVIDADES_CREAR_REPORTE->value,
                PermisosEnum::CLASES_VER->value,
                PermisosEnum::EVENTOS_VER->value,
            ]
        ];
        $perm = $rolePermissions[$this->value] ?? [];
        return $perm;
    }
}

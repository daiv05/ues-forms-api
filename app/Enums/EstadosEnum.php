<?php

namespace App\Enums;

enum EstadosEnum: int
{
    case ACTIVO = 1;
    case INACTIVO = 2;
    case PENDIENTE = 3;
    case EN_EDICION = 4;
    case RECHAZADO = 5;
    case APROBADO = 6;
    case BLOQUEADO = 7;

    // Estados de encuesta
    public static function encuestas(): array
    {
        return [
            self::ACTIVO,
            self::INACTIVO,
            self::EN_EDICION,
        ];
    }

    // Estados de usuario
    public static function usuarios(): array
    {
        return [
            self::ACTIVO,
            self::INACTIVO,
            self::BLOQUEADO,
        ];
    }

    // Estados de solicitud
    public static function solicitudes(): array
    {
        return [
            self::PENDIENTE,
            self::RECHAZADO,
            self::APROBADO,
        ];
    }
}

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
}
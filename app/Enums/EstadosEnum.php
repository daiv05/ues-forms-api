<?php

namespace App\Enums;

enum EstadosEnum: int
{
    case ASIGNADO = 1;
    case EN_PROCESO = 2;
    case EN_PAUSA = 3;
    case COMPLETADO = 4;
    case FINALIZADO = 5;
    case INCOMPLETO = 6;
}
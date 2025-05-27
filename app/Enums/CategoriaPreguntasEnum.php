<?php

namespace App\Enums;

enum CategoriaPreguntasEnum: string
{
    case TEXTO_CORTO = 'short_text';
    case TEXTO_LARGO = 'long_text';
    case SELECCION_MULTIPLE = 'multiple_choice';
    case SELECCION_UNICA = 'single_choice';
    case ORDENAMIENTO = 'ranking';
    case ESCALA_NUMERICA = 'numeric_scale';
    case ESCALA_LIKERT = 'likert_scale';
    case FALSO_VERDADERO = 'true_false';

    public function id(): int
    {
        return match($this) {
            self::TEXTO_CORTO => 1,
            self::TEXTO_LARGO => 2,
            self::SELECCION_MULTIPLE => 3,
            self::SELECCION_UNICA => 4,
            self::ORDENAMIENTO => 5,
            self::ESCALA_NUMERICA => 6,
            self::ESCALA_LIKERT => 7,
            self::FALSO_VERDADERO => 8,
        };
    }

    public function fieldsRequired(): array
    {
        return match($this) {
            self::TEXTO_CORTO => [],
            self::TEXTO_LARGO => [],
            self::SELECCION_MULTIPLE => ['options'],
            self::SELECCION_UNICA => ['options'],
            self::ORDENAMIENTO => ['options'],
            self::ESCALA_NUMERICA => ['rangeFrom', 'rangeTo'],
            self::ESCALA_LIKERT => ['options'],
            self::FALSO_VERDADERO => ['options'],
        };
    }
}

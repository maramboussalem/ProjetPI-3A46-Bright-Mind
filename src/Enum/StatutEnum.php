<?php

// src/Enum/StatutEnum.php

namespace App\Enum;

enum StatutEnum: string
{
    case EN_COURS = 'En cours';
    case RESOLU = 'Résolu';
    case FINI = 'Fini';

    // Optional: Custom methods if needed
    public static function getValues(): array
    {
        return [
            self::EN_COURS->value,
            self::RESOLU->value,
            self::FINI->value,
        ];
    }

    public static function getLabels(): array
    {
        return [
            self::EN_COURS->value => 'En cours',
            self::RESOLU->value => 'Résolu',
            self::FINI->value => 'Fini',
        ];
    }
}



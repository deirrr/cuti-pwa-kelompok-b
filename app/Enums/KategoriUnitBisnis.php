<?php

namespace App\Enums;

enum KategoriUnitBisnis: string
{
    case HeadOffice = 'head_office';
    case Operasional = 'operasional';

    public function label(): string
    {
        return match ($this) {
            self::HeadOffice => 'Head Office',
            self::Operasional => 'Unit operasional',
        };
    }
}

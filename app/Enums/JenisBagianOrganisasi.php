<?php

namespace App\Enums;

enum JenisBagianOrganisasi: string
{
    case Direktorat = 'direktorat';
    case Departemen = 'departemen';
    case Bagian = 'bagian';

    public function label(): string
    {
        return match ($this) {
            self::Direktorat => 'Direktorat',
            self::Departemen => 'Departemen',
            self::Bagian => 'Bagian',
        };
    }
}

<?php

namespace App\Enums;

enum PeranPengguna: string
{
    case Karyawan = 'karyawan';
    case Atasan = 'atasan';
    case AdminHr = 'admin_hr';

    public function label(): string
    {
        return match ($this) {
            self::Karyawan => 'Staff',
            self::Atasan => 'Atasan',
            self::AdminHr => 'Admin HR',
        };
    }
}

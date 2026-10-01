<?php

namespace App\Enums;

enum StatusPengajuanCuti: string
{
    case Draf = 'draf';
    case MenungguAtasan = 'menunggu_atasan';
    case MenungguHr = 'menunggu_hr';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Draf => 'Draf',
            self::MenungguAtasan => 'Menunggu Atasan',
            self::MenungguHr => 'Menunggu HR',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}

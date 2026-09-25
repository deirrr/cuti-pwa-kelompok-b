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
}

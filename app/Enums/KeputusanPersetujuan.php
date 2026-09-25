<?php

namespace App\Enums;

enum KeputusanPersetujuan: string
{
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';
}

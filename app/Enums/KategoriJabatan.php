<?php

namespace App\Enums;

enum KategoriJabatan: string
{
    case DirekturUtama = 'direktur_utama';
    case Direktur = 'direktur';
    case KepalaDepartemen = 'kepala_departemen';
    case ManagerOperasional = 'manager_operasional';
    case Supervisor = 'supervisor';
    case Staf = 'staf';
    case Lainnya = 'lainnya';
}

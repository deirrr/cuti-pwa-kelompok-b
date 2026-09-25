<?php

namespace App\Enums;

enum PeranPengguna: string
{
    case Karyawan = 'karyawan';
    case Atasan = 'atasan';
    case AdminHr = 'admin_hr';
}

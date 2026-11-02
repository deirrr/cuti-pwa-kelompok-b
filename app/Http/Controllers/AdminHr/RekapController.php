<?php

namespace App\Http\Controllers\AdminHr;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RekapController extends Controller
{
    public function __invoke(): View
    {
        return view('admin-hr.rekap.index');
    }
}

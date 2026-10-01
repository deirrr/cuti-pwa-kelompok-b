<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminHrSaldoCutiTest extends TestCase
{
    public function test_modul_saldo_cuti_mandiri_tidak_dapat_diakses(): void
    {
        $this->get('/admin-hr/saldo-cuti')->assertNotFound();
        $this->get('/admin-hr/saldo-cuti/1/ubah')->assertNotFound();
        $this->put('/admin-hr/saldo-cuti/1')->assertNotFound();
    }
}

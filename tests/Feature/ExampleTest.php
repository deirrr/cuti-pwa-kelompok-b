<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_halaman_utama_mengarahkan_pengguna_ke_halaman_masuk(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}

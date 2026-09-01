<?php

namespace Tests\Unit;

use App\Http\Controllers\InvestigasiController;
use App\Models\Investigasi;
use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_user_model_implements_jwt_subject(): void
    {
        $this->assertTrue(is_a(User::class, JWTSubject::class, true));
    }

    public function test_investigasi_model_includes_tgl_kirim_laporan_field(): void
    {
        $this->assertContains('tgl_kirim_laporan', (new Investigasi())->getFillable());
    }

    public function test_admin_role_is_case_insensitive_for_spv_and_master(): void
    {
        $this->assertContains(strtolower('SPV'), ['spv', 'master']);
        $this->assertContains(strtolower('MASTER'), ['spv', 'master']);
    }

    public function test_generate_number_case_for_selected_insurer(): void
    {
        $controller = $this->app->make(InvestigasiController::class);

        $this->assertIsInt($controller->generateNumberCaseForAsuransi(999));
        $this->assertSame('ASURANSI/' . date('n') . '.' . date('Y') . '/1', $controller->generateNoCase(null, 1));
    }
}

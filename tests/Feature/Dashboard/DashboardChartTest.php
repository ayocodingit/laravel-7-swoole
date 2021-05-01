<?php

namespace Tests\Feature\Dashboard;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\KesimpulanPemeriksaanEnum;
use App\Models\Fasyankes;
use App\Models\Kota;
use App\Models\Pasien;
use App\Models\PasienRegister;
use App\Models\Register;
use App\Models\Sampel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class DashboardChartTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->artisan('db:seed --class=StatusSampelSeeder');
        $this->kota = factory(Kota::class)->create();
        $this->fasyankes = factory(Fasyankes::class)->create();
        $this->register = factory(Register::class)->create(['creator_user_id' => $this->user->id]);
        $this->pasien = factory(Pasien::class)->create();
        factory(PasienRegister::class)->create(['register_id' => $this->register->id, 'pasien_id' => $this->pasien->id]);
        $this->sampel = factory(Sampel::class)->create([
            'nomor_register' => $this->register->nomor_register,
            'register_id' => $this->register->id,
        ]);
        $this->artisan('command:dashboard_chart');
        $this->artisan('schedule:run');
    }

    public function testRegistrasiDailyMandiri()
    {
        $params = [
            'tipe' => 'Daily',
            'jenis' => JenisRegistrasiEnum::mandiri()
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/regis', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testRegistrasiMonthlyMandiri()
    {
        $params = [
            'tipe' => 'Monthly',
            'jenis' => JenisRegistrasiEnum::mandiri()
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/regis', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testRegistrasiDailyRujukan()
    {
        $params = [
            'tipe' => 'Daily',
            'jenis' => JenisRegistrasiEnum::rujukan()
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/regis', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testRegistrasiMonthlyRujukan()
    {
        $params = [
            'tipe' => 'Monthly',
            'jenis' => JenisRegistrasiEnum::rujukan()
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/regis', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testSampelDaily()
    {
        $params = [
            'tipe' => 'Daily',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/sampel', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testSampelMonthly()
    {
        $params = [
            'tipe' => 'Monthly',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/sampel', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testEkstraksiDaily()
    {
        $params = [
            'tipe' => 'Daily',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/ekstraksi', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testEkstraksiMonthly()
    {
        $params = [
            'tipe' => 'Monthly',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/ekstraksi', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testPCRDaily()
    {
        $params = [
            'tipe' => 'Daily',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/pcr', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testPCRMonthly()
    {
        $params = [
            'tipe' => 'Monthly',
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/pcr', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testPositifDaily()
    {
        $params = [
            'tipe' => 'Daily',
            'jenis' => KesimpulanPemeriksaanEnum::positif(),
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/positif-negatif', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testPositifMonthly()
    {
        $params = [
            'tipe' => 'Monthly',
            'jenis' => KesimpulanPemeriksaanEnum::positif(),
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/positif-negatif', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testNegatifDaily()
    {
        $params = [
            'tipe' => 'Daily',
            'jenis' => KesimpulanPemeriksaanEnum::negatif(),
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/positif-negatif', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }

    public function testNegatifMonthly()
    {
        $params = [
            'tipe' => 'Monthly',
            'jenis' => KesimpulanPemeriksaanEnum::negatif(),
        ];
        $this->actingAs($this->user)->json('GET', '/api/v2/chart/positif-negatif', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['label', 'value']);
    }
}

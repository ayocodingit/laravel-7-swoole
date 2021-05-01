<?php

namespace Tests\Feature\Register;

use App\Enums\JenisRegistrasiEnum;
use App\Models\Fasyankes;
use App\Models\Kota;
use App\Models\Pasien;
use App\Models\PasienRegister;
use App\Models\Register;
use App\Models\Sampel;
use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class LogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=StatusSampelSeeder');
        $this->kota = factory(Kota::class)->create();
        $this->user = factory(User::class)->create();
        $this->fasyankes = factory(Fasyankes::class)->create();
        $this->register = factory(Register::class)->create([
            'creator_user_id' => $this->user->id,
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan(),
        ]);
        $this->pasien = factory(Pasien::class)->create();
        factory(PasienRegister::class)->create(['register_id' => $this->register->id, 'pasien_id' => $this->pasien->id]);
        $this->sampel = factory(Sampel::class)->create([
            'nomor_register' => $this->register->nomor_register,
            'register_id' => $this->register->id,
        ]);
    }

    public function testLog()
    {
        $this->actingAs($this->user)->getJson("/api/v1/register/logs/{$this->register->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result']);
    }

    public function testLogInvalidRegisterId()
    {
        $this->actingAs($this->user)->getJson("/api/v1/register/logs/01293")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result']);
    }
}

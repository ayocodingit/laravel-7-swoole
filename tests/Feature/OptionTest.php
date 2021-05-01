<?php

namespace Tests\Feature;

use App\Models\JenisVTM;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\ReagenEkstraksi;
use App\Models\ReagenPCR;
use App\Models\Validator;
use App\Role;
use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class OptionTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testRoleOption()
    {
        factory(Role::class)->create();
        $this->actingAs($this->user)->getJson('/api/roles-option')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testLabPcrOption()
    {
        $this->artisan('db:seed --class=LabPCRSeeder');
        $this->actingAs($this->user)->getJson('/api/lab-pcr-option')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testJenisSampelOption()
    {
        $this->artisan('db:seed --class=JenisSampelSeeder');
        $this->actingAs($this->user)->getJson('/api/jenis-sampel-option')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testJenisVtmOption()
    {
        factory(JenisVTM::class)->create();
        $this->actingAs($this->user)->getJson('/api/jenis-vtm')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testValidatorOption()
    {
        factory(Validator::class)->create();
        $this->actingAs($this->user)->getJson('/api/validator-option')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testNegaraOption()
    {
        factory(Negara::class)->create();
        $this->actingAs($this->user)->getJson('/api/negara-option')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'text']]);
    }

    public function testProvinsiOption()
    {
        factory(Provinsi::class)->create();
        $this->actingAs($this->user)->getJson('/api/provinsi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'nama']]);
    }

    public function testKecamatanOption()
    {
        factory(Kecamatan::class)->create();
        $this->actingAs($this->user)->getJson('/api/kecamatan?kota_id=1')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'nama']]);
    }

    public function testKelurahanOption()
    {
        factory(Kelurahan::class)->create();
        $this->actingAs($this->user)->getJson('/api/kelurahan?kecamatan_id=1')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['id', 'nama']]);
    }

    public function testReagenEkstraksiOption()
    {
        factory(ReagenEkstraksi::class)->create();
        $this->actingAs($this->user)->getJson('/api/v1/list/reagen-ekstraksi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                '0' => [
                    'id',
                    'nama',
                    'metode_ekstraksi',
                ],
            ]);
    }

    public function testReagenPCROption()
    {
        factory(ReagenPCR::class)->create();
        $this->actingAs($this->user)->getJson('/api/v1/list/reagen-pcr')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                '0' => [
                    'id',
                    'nama',
                    'ct_normal',
                ],
            ]);
    }
}

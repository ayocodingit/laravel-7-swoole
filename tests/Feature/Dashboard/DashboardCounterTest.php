<?php

namespace Tests\Feature\Dashboard;

use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class DashboardCounterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->artisan('command:dashboard_counter');
    }

    public function testTrackingProgress()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/tracking-progress')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'registration',
                    'sampel',
                    'ekstraksi',
                    'rtpcr',
                    'verifikasi',
                    'validasi'
                ]
            ]);
    }

    public function testPositifNegatif()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/positif-negatif')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'positif',
                    'negatif',
                ]
            ]);
    }

    public function testPasienDiperiksa()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/pasien-diperiksa')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'kontak_erat',
                    'suspek',
                    'probable',
                    'konfirmasi',
                    'tanpa_kriteria',
                ]
            ]);
    }

    public function testRegistrasi()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/registrasi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'mandiri',
                    'rujukan',
                    'total',
                    'jumlah_perhari_mandiri',
                    'jumlah_perhari_rujukan',
                    'data_belum_lengkap_mandiri',
                    'data_belum_lengkap_rujukan',
                    'pemeriksaan_selesai_mandiri',
                    'pemeriksaan_selesai_rujukan',
                    'belum_input_rujukan',
                ]
            ]);
    }

    public function testAdminSampel()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/admin-sampel')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'jumlah_perhari_sampel',
                    'sampel_register_mandiri',
                    'total_sampel',
                ]
            ]);
    }

    public function testEkstraksi()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/ekstraksi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'jumlah_perhari_ektraksi',
                    'sampel_baru',
                    'ekstraksi',
                    'kirim',
                    'sampel_invalid',
                ]
            ]);
    }

    public function testPCR()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/pcr')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'sampel_baru',
                    'jumlah_perhari_pcr',
                    'hasil_pemeriksaan',
                    're_pcr',
                ]
            ]);
    }

    public function testVerifikasi()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/verifikasi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'belum_diverifikasi',
                    'terverifikasi',
                ]
            ]);
    }

    public function testValidasi()
    {
        $this->actingAs($this->user)->getJson('/api/v2/dashboard/validasi')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result' => [
                    'belum_divalidasi',
                    'tervalidasi',
                ]
            ]);
    }
}

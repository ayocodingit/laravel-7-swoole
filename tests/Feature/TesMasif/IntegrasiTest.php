<?php

namespace Tests\Feature\TesMasif;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\StatusPasienEnum;
use App\Models\Fasyankes;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class IntegrasiTest extends TestCase
{
    protected $data = [
        'api_key' => null,
        'data' => [
            [
                'kewarganegaraan' => 'WNI',
                'kategori' => 'test',
                'nama_pasien' => 'test',
                'nik' => '1234567890123456',
                'registration_code' => 'L002321',
                'tempat_lahir' => 'ndasdsa',
                'jenis_kelamin' => 'L',
                'provinsi_id' => null,
                'kota_id' => null,
                'kecamatan_id' => null,
                'kelurahan_id' => null,
                'alamat' => 'test',
                'rt' => null,
                'rw' => null,
                'no_hp' => 124324324,
                'suhu' => null,
                'nomor_sampel' => null,
                'keterangan' => null,
                'hasil_rdt' => null,
                'usia_tahun' => 23,
                'usia_bulan' => 07,
                'kunjungan' => 1,
                'jenis_registrasi' => null,
                'fasyankes_id' => null,
                'rs_kunjungan' => 'test',
            ]
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->data['data'][0]['tanggal_lahir'] = Carbon::now();
        $this->data['data'][0]['tanggal_kunjungan'] = Carbon::now();
        $this->data['data'][0]['kriteria'] = StatusPasienEnum::tanpa_kriteria();
        $this->data['api_key'] = config('services.tes_masif.api_key');
        $this->fasyankes = factory(Fasyankes::class)->create();
    }

    public function testMandiri()
    {
        $this->data['data'][0]['nomor_sampel'] = 'L00001';
        $this->data['data'][0]['jenis_registrasi'] = JenisRegistrasiEnum::mandiri();
        $this->postJson('/api/v1/tes-masif/bulk', $this->data)
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonStructure([
                 'message',
                 'result' => ['berhasil', 'gagal']
            ]);
    }

    public function testMandiriInvalidSampel()
    {
        $this->data['data'][0]['nomor_sampel'] = 'LL0001';
        $this->data['data'][0]['jenis_registrasi'] = JenisRegistrasiEnum::mandiri();
        $this->postJson('/api/v1/tes-masif/bulk', $this->data)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure([
                 'message',
                 'result' => [
                     'berhasil',
                     'gagal' => [
                            '0' => [
                                'nomor_sampel',
                                'message',
                                'result'
                            ]
                     ]
                 ]
            ]);
    }

    public function testRujukan()
    {
        $this->data['data'][0]['nomor_sampel'] = 'L00001';
        $this->data['data'][0]['jenis_registrasi'] = JenisRegistrasiEnum::rujukan();
        $this->data['data'][0]['fasyankes_id'] = $this->fasyankes->id;
        $this->postJson('/api/v1/tes-masif/bulk', $this->data)
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonStructure([
                 'message',
                 'result' => ['berhasil', 'gagal']
            ]);
    }

    public function testRujukanFasyankesId()
    {
        $this->data['data'][0]['nomor_sampel'] = 'LL0001';
        $this->data['data'][0]['jenis_registrasi'] = JenisRegistrasiEnum::rujukan();
        $this->data['data'][0]['fasyankes_id'] = null;
        $this->postJson('/api/v1/tes-masif/bulk', $this->data)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure([
                 'message',
                 'result' => [
                     'berhasil',
                     'gagal' => [
                            '0' => [
                                'nomor_sampel',
                                'message',
                                'result'
                            ]
                     ]
                 ]
            ]);
    }

    public function testTryCatchError()
    {
        $this->data['data'][0]['nomor_sampel'] = 'L0001';
        $this->data['data'][0]['jenis_registrasi'] = JenisRegistrasiEnum::mandiri();
        $this->data['data'][0]['jenis_kelamin'] = 123;
        $this->postJson('/api/v1/tes-masif/bulk', $this->data)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure([
                 'message',
                 'result' => [
                     'berhasil',
                     'gagal' => [
                            '0' => [
                                'nomor_sampel',
                                'message',
                                'result'
                            ]
                     ]
                 ]
            ]);
    }
}

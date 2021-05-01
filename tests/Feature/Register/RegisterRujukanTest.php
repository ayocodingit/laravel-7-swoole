<?php

namespace Tests\Feature\Register;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\KewarganegaraanEnum;
use App\Models\Fasyankes;
use App\Models\Kota;
use App\Models\Pasien;
use App\Models\PasienRegister;
use App\Models\Register;
use App\Models\Sampel;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterRujukanTest extends TestCase
{
    use WithFaker;

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

        $this->data = [
            "reg_kota_id" => $this->kota->id,
            "reg_kewarganegaraan" => $this->faker->randomElement(KewarganegaraanEnum::getAll()),
            "reg_sumberpasien" => "Rujukan",
            "reg_sumberpasien_isian" => "Rujukan",
            "reg_nama_pasien" => $this->faker->name,
            "reg_alamat" => $this->faker->address,
            "reg_nohp" => rand(),
            "reg_fasyankes_pengirim" => "1",
            "reg_no" => "R1996",
            "reg_fasyankes_id" => $this->fasyankes->id,
            "reg_nama_rs" => $this->faker->name,
            "samples" => [
                [
                    "nomor_sampel" => $this->sampel->nomor_sampel,
                    "id" => $this->sampel->id,
                    "jenis_sampel" => null,
                    "kondisi_sampel" => null,
                ],
            ],
        ];
    }

    public function testRequestNomorRegistrasi()
    {
        $this->actingAs($this->user)->getJson('/api/v1/register/noreg', ['tipe' => 'rujukan'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result']);
    }

    public function testGetData()
    {
        $params = [
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan(),
            'page' => 1,
            'perpage' => 20,
            'search' => null,
            'order' => 'tgl_input',
            'order_direction' => 'desc',
        ];
        $this->actingAs($this->user)
            ->json('GET', '/api/registrasi-mandiri', $params)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "0" => [
                        "nomor_register",
                        "register_id",
                        "pasien_id",
                        "usia_bulan",
                        "tgl_input",
                        "jenis_registrasi",
                        "nomor_sampel",
                        "sampel_status",
                        "sumber_pasien",
                        "status",
                        "nama_lengkap",
                        "nik",
                        "usia_tahun",
                        "tanggal_lahir",
                        "tempat_lahir",
                        "jenis_kelamin",
                        "alamat_lengkap",
                        "nama_kota",
                        "provinsi",
                        "kecamatan",
                        "kelurahan",
                        "no_rt",
                        "no_rw",
                        "no_hp",
                        "no_telp",
                        "fasyankes_pengirim",
                        "nama_dokter",
                        "kunjungan_ke",
                        "created_at",
                        "deskripsi",
                        "fasyankes_nama",
                    ],
                ],
                "links" => [
                    "first",
                    "last",
                    "prev",
                    "next",
                ],
                "meta" => [
                    "current_page",
                    "from",
                    "last_page",
                    "path",
                    "per_page",
                    "to",
                    "total",
                ],
            ]);
    }

    public function testStore()
    {
        $this->actingAs($this->user)->postJson('/api/registrasi-rujukan/store', $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('register', [
            'creator_user_id' => $this->user->id,
            'sumber_pasien' => $this->data['reg_sumberpasien'],
            'fasyankes_pengirim' => $this->data['reg_fasyankes_pengirim'],
            'fasyankes_id' => $this->data['reg_fasyankes_id'],
            'nama_rs' => $this->fasyankes->nama,
        ]);

        $this->assertDatabaseHas('pasien', [
            'nama_lengkap' => $this->data['reg_nama_pasien'],
            'kewarganegaraan' => $this->data['reg_kewarganegaraan'],
            'alamat_lengkap' => $this->data['reg_alamat'],
            'no_hp' => $this->data['reg_nohp'],
        ]);
    }

    public function testStoreNullData()
    {
        $this->actingAs($this->user)->postJson('/api/registrasi-rujukan/store', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testUpdate()
    {
        $this->actingAs($this->user)
            ->postJson("/api/registrasi-rujukan/update/{$this->register->id}/{$this->pasien->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('register', [
            'creator_user_id' => $this->user->id,
            'sumber_pasien' => $this->data['reg_sumberpasien'],
            'fasyankes_pengirim' => $this->data['reg_fasyankes_pengirim'],
            'fasyankes_id' => $this->data['reg_fasyankes_id'],
            'nama_rs' => $this->fasyankes->nama,
        ]);

        $this->assertDatabaseHas('pasien', [
            'nama_lengkap' => $this->data['reg_nama_pasien'],
            'kewarganegaraan' => $this->data['reg_kewarganegaraan'],
            'alamat_lengkap' => $this->data['reg_alamat'],
            'no_hp' => $this->data['reg_nohp'],
        ]);
    }

    public function testUpdateNullData()
    {
        $this->actingAs($this->user)
            ->postJson("/api/registrasi-rujukan/update/{$this->register->id}/{$this->pasien->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testUpdateInvalidRegisterId()
    {
        $this->actingAs($this->user)
            ->postJson("/api/registrasi-rujukan/update/0/{$this->pasien->id}", $this->data)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error']);
    }

    public function testUpdateInvalidPasienId()
    {
        $this->actingAs($this->user)
            ->postJson("/api/registrasi-rujukan/update/{$this->register->id}/0", $this->data)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error']);
    }

    public function testShowData()
    {
        $this->actingAs($this->user)
            ->getJson("/api/registrasi-rujukan/update/{$this->register->id}/{$this->pasien->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "result" => [
                    "reg_no",
                    "reg_kewarganegaraan",
                    "reg_sumberpasien",
                    "reg_sumberpasien_isian",
                    "reg_nama_pasien",
                    "reg_nik",
                    "reg_tempatlahir",
                    "reg_tgllahir",
                    "reg_nohp",
                    "reg_provinsi_id",
                    "reg_provinsi",
                    "reg_kota_id",
                    "reg_kota",
                    "reg_kecamatan_id",
                    "reg_kecamatan",
                    "reg_kelurahan_id",
                    "reg_kelurahan",
                    "reg_alamat",
                    "reg_rt",
                    "reg_rw",
                    "reg_suhu",
                    "samples" => [
                        [
                            "sampel_status",
                            "nomor_sampel",
                            "id",
                        ],
                    ],
                    "reg_keterangan",
                    "reg_jk",
                    "reg_kunke",
                    "reg_tanggalkunjungan",
                    "reg_rs_kunjungan",
                    "reg_fasyankes_pengirim",
                    "reg_telp_fas_pengirim",
                    "reg_nama_dokter",
                    "reg_nama_rs",
                    "reg_nama_rs_lainnya",
                    "daerahlain",
                    "reg_dinkes_pengirim",
                    "reg_rsfasyankes",
                    "reg_usia_tahun",
                    "reg_usia_bulan",
                    "reg_hasil_rdt",
                    "reg_fasyankes_id",
                    "nama_rs",
                    "fasyankes_pengirim",
                    "status",
                    "pelaporan_id",
                    "pelaporan_id_case",
                ],
            ]);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/registrasi-rujukan/delete/{$this->register->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertSoftDeleted('register', ['id' => $this->register->id]);
    }

    public function testDeleteInvalidRegisterId()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/registrasi-rujukan/delete/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error']);
    }

    public function testCekDataValidNomorSampel()
    {
        $sampel = factory(Sampel::class)->create(['nomor_sampel' => rand()]);
        $data = ['nomor_sampel' => $sampel->nomor_sampel];
        $this->actingAs($this->user)->postJson("/api/registrasi-rujukan/cek", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['result' => [
                'id' => $sampel->id,
                'nomor_sampel' => $sampel->nomor_sampel,
            ]]);
    }

    public function testCekDataInvalidalidNomorSampel()
    {
        $data = ['nomor_sampel' => '9999999'];
        $this->actingAs($this->user)->postJson("/api/registrasi-rujukan/cek", $data)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testCekDataValidNomorSampelDataPasien()
    {
        $register = factory(Register::class)->create([
            'creator_user_id' => $this->user->id,
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan(),
        ]);
        $nomorSampel = factory(Sampel::class)->create(['register_id' => $register->id])->nomor_sampel;
        $data = ['nomor_sampel' => $nomorSampel];
        $this->actingAs($this->user)->postJson("/api/registrasi-rujukan/cek", $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testExportRujukan()
    {
        $this->actingAs($this->user)
            ->getJson("/api/registrasi-rujukan/export-excel-rujukan?jenis_registrasi=rujukan")
            ->assertStatus(Response::HTTP_OK);
    }
}

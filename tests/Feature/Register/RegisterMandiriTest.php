<?php

namespace Tests\Feature\Register;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\StatusPasienEnum;
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

class RegisterMandiriTest extends TestCase
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
            'jenis_registrasi' => JenisRegistrasiEnum::mandiri(),
        ]);
        $this->pasien = factory(Pasien::class)->create();
        factory(PasienRegister::class)->create([
            'register_id' => $this->register->id,
            'pasien_id' => $this->pasien->id,
        ]);
        $this->sampel = factory(Sampel::class)->create([
            'nomor_register' => $this->register->nomor_register,
            'register_id' => $this->register->id,
        ]);

        $this->data = [
            'reg_no' => 'L202102150002',
            'reg_kewarganegaraan' => $this->faker->randomElement(KewarganegaraanEnum::getAll()),
            'reg_sumberpasien' => 'Umum',
            'reg_nama_pasien' => $this->faker->name,
            'reg_alamat' => $this->faker->address,
            'reg_nohp' => rand(),
            'reg_sampel' => 'l1234567890',
            'status' => $this->faker->randomElement(StatusPasienEnum::getIndices()),
            'reg_kota_id' => $this->kota->id,
            'reg_provinsi_id' => $this->kota->provinsi_id,
        ];
    }

    public function testRequestNomorRegistrasi()
    {
        $this->actingAs($this->user)->getJson('/api/v1/register/noreg', ['tipe' => 'mandiri'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['result']);
    }

    public function testGetData()
    {
        $params = [
            'jenis_registrasi' => JenisRegistrasiEnum::mandiri(),
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

    public function testShowData()
    {
        $this->actingAs($this->user)
            ->getJson("/api/v1/register/mandiri/{$this->register->id}/{$this->pasien->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "result" => [
                    "alamat_lengkap",
                    "fasyankes_nama",
                    "jenis_kelamin",
                    "kecamatan",
                    "kecamatan_id",
                    "kelurahan",
                    "kelurahan_id",
                    "keterangan_lain",
                    "kewarganegaraan",
                    "kota",
                    "kota_id",
                    "kunjungan_ke",
                    "nama_lengkap",
                    "nik",
                    "no_hp",
                    "no_rt",
                    "no_rw",
                    "nomor_register",
                    "nomor_sampel",
                    "provinsi",
                    "provinsi_id",
                    "rs_kunjungan",
                    "sampel_id",
                    "status",
                    "suhu",
                    "sumber_pasien",
                    "tanggal_kunjungan",
                    "tanggal_lahir",
                    "tempat_lahir",
                    "usia_bulan",
                    "usia_tahun",
                ],
            ]);
    }

    public function testStore()
    {
        $this->actingAs($this->user)->postJson('/api/v1/register/mandiri', $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('register', [
            'creator_user_id' => $this->user->id,
            'sumber_pasien' => $this->data['reg_sumberpasien'],
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
        $this->actingAs($this->user)->postJson('/api/v1/register/mandiri', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testUpdate()
    {
        $this->data += [
            'sampel_id' => $this->sampel->id,
            'reg_sampel' => "l9382323",
        ];

        $this->actingAs($this->user)
            ->postJson("/api/v1/register/mandiri/update/{$this->register->id}/{$this->pasien->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('register', [
            'creator_user_id' => $this->user->id,
            'sumber_pasien' => $this->data['reg_sumberpasien'],
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
            ->postJson("/api/v1/register/mandiri/update/{$this->register->id}/{$this->pasien->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/v1/register/mandiri/{$this->register->id}/{$this->pasien->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertSoftDeleted('register', ['id' => $this->register->id]);
    }

    public function testDeleteInvalidId()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/v1/register/mandiri/0/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error']);
    }

    public function testExportMandiri()
    {
        $this->actingAs($this->user)
            ->getJson("/api/registrasi-mandiri/export-excel?jenis_registrasi=mandiri")
            ->assertStatus(Response::HTTP_OK);
    }
}

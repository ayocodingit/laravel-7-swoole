<?php

namespace Tests\Feature;

use App\Enums\JenisSampelEnum;
use App\Enums\SumberSampelEnum;
use App\Models\JenisVTM;
use App\Models\Sampel;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class SampelTest extends TestCase
{
    use WithFaker;

    private $responseStructure = [
        "id",
        "nomor_sampel",
        "nomor_register",
        "register_id",
        "jenis_sampel_nama",
        "waktu_waiting_sample",
        "tgl_input_sampel",
        "waktu_sample_taken",
        "petugas_pengambilan_sampel",
        "nama_vtm",
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->artisan('db:seed --class=StatusSampelSeeder');
        $this->artisan('db:seed --class=JenisSampelSeeder');

        $this->sampel = factory(Sampel::class)->create();
        $this->vtm = factory(JenisVTM::class)->create();

        $this->value_rujukan = $this->faker->randomElement([
            SumberSampelEnum::rujukanDinkes(),
            SumberSampelEnum::rujukanRs(),
        ]);

        $this->data = [
            'sam_jenis_sampel' => rand(1, 11),
            'petugas_pengambil' => $this->faker->name(),
            'vtm' => $this->vtm->nama,
            'pen_catatan' => $this->faker->paragraph(),
            'pen_nik' => '1234567898765432',
            'pen_noreg' => $this->faker->randomNumber(),
            'pen_penerima_sampel' => $this->faker->name(),
            'pukulsampel' => date('H:i'),
            'tanggalsampel' => Carbon::now(),
        ];

        $this->sampel_database = [
            'jenis_sampel_id' => $this->data['sam_jenis_sampel'],
            'sampel_status' => 'sample_taken',
            'tanggal_pengambilan_sampel' => $this->data['tanggalsampel'],
            'jam_pengambilan_sampel' => $this->data['pukulsampel'],
            'petugas_pengambilan_sampel' => $this->data['petugas_pengambil'],
            'waktu_sample_taken' => Carbon::parse($this->data['tanggalsampel'])->format('Y-m-d') . ' ' . $this->data['pukulsampel'],
            'jenis_vtm' => $this->data['vtm'],
        ];

        $this->pengambilan_sampel_database = [
            'penerima_sampel' => $this->data['pen_penerima_sampel'],
            'sampel_diterima' => true,
            'catatan' => $this->data['pen_catatan'],
        ];
    }

    public function testList()
    {
        $this->actingAs($this->user)->getJson("/api/sample/get-data")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseStructure,
                ],
            ]);
    }

    public function testListWithParams()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'sample_taken']);
        $this->actingAs($this->user)->getJson("/api/sample/get-data?sampel_status=sample_taken&sampel_lengkap=true")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseStructure,
                ],
            ]);
    }

    public function testStoreSampelMandiri()
    {
        $this->data['pen_sampel_sumber'] = SumberSampelEnum::mandiri();
        $this->data['nomorsampel'] = "L" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->pengambilan_sampel_database['sumber_sampel'] = $this->data['pen_sampel_sumber'];
        $this->assertDatabaseHas('pengambilan_sampel', $this->pengambilan_sampel_database);
        $this->assertDatabaseHas('sampel', $this->sampel_database);
    }

    public function testStoreSampelMandiriInvalidNomorSampel()
    {
        $this->data['pen_sampel_sumber'] = SumberSampelEnum::mandiri();
        $this->data['nomorsampel'] = "R" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testStoreSampelRujukan()
    {
        $this->data['pen_sampel_sumber'] = $this->value_rujukan;
        $this->data['nomorsampel'] = "R" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->pengambilan_sampel_database['sumber_sampel'] = $this->data['pen_sampel_sumber'];
        $this->assertDatabaseHas('pengambilan_sampel', $this->pengambilan_sampel_database);
        $this->assertDatabaseHas('sampel', $this->sampel_database);
    }

    public function testStoreSampelRujukanInvalidNomorSampel()
    {
        $this->data['pen_sampel_sumber'] = $this->value_rujukan;
        $this->data['nomorsampel'] = "L" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testStoreSampelMandiriLuarJenis()
    {
        $this->data['sam_jenis_sampel'] = JenisSampelEnum::luarJenis()->getIndex();
        $this->data['sam_namadiluarjenis'] = $this->faker->name();
        $this->data['pen_sampel_sumber'] = SumberSampelEnum::mandiri();
        $this->data['nomorsampel'] = "L" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->pengambilan_sampel_database['sumber_sampel'] = $this->data['pen_sampel_sumber'];
        $this->assertDatabaseHas('pengambilan_sampel', $this->pengambilan_sampel_database);

        $this->sampel_database['jenis_sampel_nama'] = $this->data['sam_namadiluarjenis'];
        $this->sampel_database['jenis_sampel_id'] = $this->data['sam_jenis_sampel'];
        $this->assertDatabaseHas('sampel', $this->sampel_database);
    }

    public function testStoreSampelMandiriLuarJenisInvalidNomorSampel()
    {
        $this->data['sam_jenis_sampel'] = JenisSampelEnum::luarJenis()->getIndex();
        $this->data['sam_namadiluarjenis'] = $this->faker->name();
        $this->data['pen_sampel_sumber'] = SumberSampelEnum::mandiri();
        $this->data['nomorsampel'] = "R" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testStoreSampelRujukanLuarJenis()
    {
        $this->data['sam_jenis_sampel'] = JenisSampelEnum::luarJenis()->getIndex();
        $this->data['sam_namadiluarjenis'] = $this->faker->name();
        $this->data['pen_sampel_sumber'] = $this->value_rujukan;
        $this->data['nomorsampel'] = "R" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->pengambilan_sampel_database['sumber_sampel'] = $this->data['pen_sampel_sumber'];
        $this->assertDatabaseHas('pengambilan_sampel', $this->pengambilan_sampel_database);

        $this->sampel_database['jenis_sampel_nama'] = $this->data['sam_namadiluarjenis'];
        $this->sampel_database['jenis_sampel_id'] = $this->data['sam_jenis_sampel'];
        $this->assertDatabaseHas('sampel', $this->sampel_database);
    }

    public function testStoreSampelRujukanLuarJenisInvalidNomorSampel()
    {
        $this->data['sam_jenis_sampel'] = JenisSampelEnum::luarJenis()->getIndex();
        $this->data['sam_namadiluarjenis'] = $this->faker->name();
        $this->data['pen_sampel_sumber'] = $this->value_rujukan;
        $this->data['nomorsampel'] = "L" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/add", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testStoreSampelNullData()
    {
        $this->actingAs($this->user)->postJson("/api/sample/add", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testShowSampel()
    {
        $this->actingAs($this->user)->getJson("/api/sample/edit/{$this->sampel->nomor_sampel}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "result" => [
                    "sampel_id",
                    "pengambilan_sampel_id",
                    "register_id",
                    "sampel_status",
                    "jenis_registrasi",
                    "sumber_sampel",
                    "penerima_sampel",
                    "catatan",
                    "petugas_pengambil",
                    "pukulsampel",
                    "sam_jenis_sampel",
                    "sam_namadiluarjenis",
                    "tanggalsampel",
                    "jenis_vtm",
                    "nomorsampel",
                ],
            ]);
    }

    public function testShowSampelInvalidNomorSampel()
    {
        $this->actingAs($this->user)->getJson("/api/sample/edit/00000")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testDestroy()
    {
        $this->actingAs($this->user)->deleteJson("/api/sample/delete/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertSoftDeleted('sampel', ['id' => $this->sampel->id]);
    }

    public function testDestroyInvalidSampelId()
    {
        $this->actingAs($this->user)->deleteJson("/api/sample/delete/000")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testUpdateSampel()
    {
        $this->data['pen_sampel_sumber'] = SumberSampelEnum::mandiri();
        $this->data['nomorsampel'] = "L" . rand();

        $this->actingAs($this->user)->postJson("/api/sample/update/{$this->sampel->id}", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->pengambilan_sampel_database['sumber_sampel'] = $this->data['pen_sampel_sumber'];
        $this->assertDatabaseHas('pengambilan_sampel', $this->pengambilan_sampel_database);
        $this->assertDatabaseHas('sampel', $this->sampel_database);
    }

    public function testUpdateSampelNullData()
    {
        $this->actingAs($this->user)->postJson("/api/sample/update/{$this->sampel->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testGetSampel()
    {
        $this->actingAs($this->user)->getJson("/api/sample/get-sample/{$this->sampel->nomor_sampel}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['nomor_sampel', 'id']])
            ->assertJson([[
                'nomor_sampel' => $this->sampel->nomor_sampel,
                'id' => $this->sampel->id,
            ]]);
    }

    public function testGetSampelInvalidNomorSampel()
    {
        $this->actingAs($this->user)->getJson("/api/sample/get-sample/00009")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([]);
    }
}

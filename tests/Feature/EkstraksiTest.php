<?php

namespace Tests\Feature;

use App\Enums\MetodeEkstraksiEnum;
use App\Models\JenisVTM;
use App\Models\Sampel;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class EkstraksiTest extends TestCase
{
    use WithFaker;

    private $responseStructure = [
        "catatan_penerimaan",
        "catatan_pengiriman",
        "deskripsi",
        "id",
        "is_musnah_ekstraksi",
        "jenis_sampel_nama",
        "kesimpulan_pemeriksaan",
        "lab_pcr_nama",
        "nomor_register",
        "nomor_sampel",
        "operator_ekstraksi",
        "petugas_pengambilan_sampel",
        "sampel_status",
        "waktu_extraction_sample_extracted",
        "waktu_extraction_sample_reextract",
        "waktu_extraction_sample_sent",
        "waktu_sample_taken",
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->artisan('db:seed --class=LabPCRSeeder');
        $this->artisan('db:seed --class=StatusSampelSeeder');
        $this->artisan('db:seed --class=JenisSampelSeeder');

        $this->sampel = factory(Sampel::class)->create();
        $this->vtm = factory(JenisVTM::class)->create();

        $this->data_update = [
            'tanggal_penerimaan_sampel' => $this->faker->date(),
            'jam_penerimaan_sampel' => $this->faker->time(),
            'petugas_penerima_sampel' => $this->faker->name(),
            'catatan_penerimaan' => $this->faker->paragraph(),
            'operator_ekstraksi' => $this->faker->name(),
            'tanggal_mulai_ekstraksi' => $this->faker->date(),
            'jam_mulai_ekstraksi' => $this->faker->time(),
            'metode_ekstraksi' => MetodeEkstraksiEnum::Manual(),
            'nama_kit_ekstraksi' => $this->faker->name(),
        ];

        $this->data_kirim_ekstraksi = [
            'tanggal_pengiriman_rna' => $this->faker->date(),
            'jam_pengiriman_rna' => $this->faker->time(),
            'nama_pengirim_rna' => $this->faker->name(),
            'lab_pcr_nama' => $this->faker->name(),
            'samples' => [[
                "nomor_sampel" => $this->sampel->nomor_sampel,
                "valid" => true,
                "error" => "",
            ]],
            'lab_pcr_id' => rand(1, 3),
            'catatan_pengiriman' => $this->faker->paragraph(),
            'lab_pcr_nama' => $this->faker->name(),
        ];
    }

    public function testList()
    {
        $this->actingAs($this->user)->getJson("/api/v2/ekstraksi/get-data")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseStructure,
                ],
            ]);
    }

    public function testListWithParams()
    {
        $this->actingAs($this->user)->getJson("/api/v2/ekstraksi/get-data?sampel_status=extraction_sample_extracted")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseStructure,
                ],
            ]);
    }

    public function testShow()
    {
        $this->actingAs($this->user)->getJson("/api/v1/ekstraksi/detail/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "counter_print_hasil",
                    "created_at",
                    "creator_user_id",
                    "deleted_at",
                    "ekstraksi",
                    "id",
                    "is_from_migration",
                    "is_musnah_ekstraksi",
                    "is_musnah_pcr",
                    "jam_pengambilan_sampel",
                    "jenis_sampel_id",
                    "jenis_sampel_nama",
                    "jenis_vtm",
                    "lab_pcr_id",
                    "lab_pcr_nama",
                    "log_ekstraksi",
                    "nomor_register",
                    "nomor_sampel",
                    "pengambilan_sampel_id",
                    "petugas_pengambilan_sampel",
                    "register_id",
                    "sampel_status",
                    "status",
                    "tanggal_pengambilan_sampel",
                    "updated_at",
                    "valid_file_id",
                    "validator_id",
                    "waktu_extraction_sample_extracted",
                    "waktu_extraction_sample_reextract",
                    "waktu_extraction_sample_sent",
                    "waktu_inkonklusif",
                    "waktu_pcr_sample_analyzed",
                    "waktu_pcr_sample_received",
                    "waktu_sample_invalid",
                    "waktu_sample_print",
                    "waktu_sample_taken",
                    "waktu_sample_valid",
                    "waktu_sample_verified",
                    "waktu_swab_ulang",
                    "waktu_waiting_sample",
                ],
            ]);
    }

    public function testShowInvalidId()
    {
        $this->actingAs($this->user)->getJson("/api/v1/ekstraksi/detail/02923")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testMusnahkan()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/musnahkan/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('sampel', ['is_musnah_ekstraksi' => true]);
        $this->assertDatabaseHas('sampel_log', ['sampel_id' => $this->sampel->id]);
    }

    public function testMusnahkanInvalidId()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/musnahkan/029393")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testUpdateSampleExtacted()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_extracted']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/edit/{$this->sampel->id}", $this->data_update)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('ekstraksi', $this->data_update);
    }

    public function testUpdateSampleExtactedNullData()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_extracted']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/edit/{$this->sampel->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testUpdateSampleExcludeExtacted()
    {
        // additional data
        $data['tanggal_pengiriman_rna'] = $this->faker->date();
        $data['jam_pengiriman_rna'] = $this->faker->time();
        $data['nama_pengirim_rna'] = $this->faker->name();
        $data['lab_pcr_nama'] = $this->faker->name();
        $data['lab_pcr_id'] = rand(1, 3);
        $data['tanggal_pengiriman_rna'] = $this->faker->date();
        $data['catatan_pengiriman'] = $this->faker->paragraph();
        $newData = array_merge($data, $this->data_update);

        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/edit/{$this->sampel->id}", $newData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $sampleExtracted = $this->data_update['tanggal_mulai_ekstraksi'] . ' ' . $this->data_update['jam_mulai_ekstraksi'];
        $sampleSent = $data['tanggal_pengiriman_rna'] . ' ' . $data['jam_pengiriman_rna'];
        $this->assertDatabaseHas('ekstraksi', $this->data_update);
        $this->assertDatabaseHas('sampel', [
            'lab_pcr_id' => $data['lab_pcr_id'],
            'waktu_extraction_sample_sent' => $sampleSent,
            'waktu_extraction_sample_extracted' => $sampleExtracted,
        ]);
    }

    public function testUpdateSampleExcludeExtactedWithoutAdditionalData()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/edit/{$this->sampel->id}", $this->data_update)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testUpdateSampleExcludeExtactedNullData()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/edit/{$this->sampel->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testTerimaEkstraksi()
    {
        $data = [
            'tanggal_penerimaan_sampel' => $this->faker->date(),
            'tanggal_mulai_ekstraksi' => $this->faker->date(),
            'jam_penerimaan_sampel' => $this->faker->time(),
            'jam_mulai_ekstraksi' => $this->faker->time(),
            'petugas_penerima_sampel' => $this->faker->name(),
            'operator_ekstraksi' => $this->faker->name(),
            'metode_ekstraksi' => $this->faker->randomElement(MetodeEkstraksiEnum::getValues()),
            'nama_kit_ekstraksi' => $this->faker->name(),
            'alat_ekstraksi' => $this->faker->name(),
            'samples' => [[
                "nomor_sampel" => $this->sampel->nomor_sampel,
                "valid" => true,
                "error" => "",
            ]],
            'catatan_penerimaan' => $this->faker->paragraph(),
        ];

        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/terima", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        unset($data['samples']);
        $this->assertDatabaseHas('ekstraksi', $data);
        $this->assertDatabaseHas('sampel', ['sampel_status' => 'extraction_sample_extracted']);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'extraction_sample_extracted',
            'description' => 'Sampel diekstraksi',
        ]);
    }

    public function testTerimaEkstraksiNullData()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/terima", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testSetInvalidSampel()
    {
        $data = ['alasan' => $this->faker->paragraph()];
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => $this->faker->randomElement(['extraction_sample_reextract', 'sample_taken'])]);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-invalid/{$this->sampel->id}", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('ekstraksi', ['user_id' => $this->user->id, 'catatan_pengiriman' => $data['alasan']]);
        $this->assertDatabaseHas('sampel', ['sampel_status' => 'sample_invalid']);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'sample_invalid',
            'description' => 'Sample marked as invalid',
        ]);
    }

    public function testSetInvalidSampelWrongSampelStatus()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_extracted']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-invalid/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testSetInvalidSampelInvalidId()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-invalid/01202")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testSetProses()
    {
        $data = ['alasan' => $this->faker->paragraph()];
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_reextract']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-proses/{$this->sampel->id}", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('ekstraksi', ['user_id' => $this->user->id, 'catatan_pengiriman' => $data['alasan']]);
        $this->assertDatabaseHas('sampel', ['sampel_status' => 'extraction_sample_extracted']);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'extraction_sample_extracted',
            'description' => 'Sampel diekstraksi',
        ]);
    }

    public function testSetProsesWrongSampelStatus()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_extracted']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-proses/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testKirimEkstraksi()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/kirim", $this->data_kirim_ekstraksi)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('sampel', ['sampel_status' => 'extraction_sample_sent']);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'extraction_sample_sent',
            'description' => 'RNA dikirim ke lab PCR',
        ]);
    }

    public function testKirimEkstraksiNullData()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/kirim", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testKirimUlangEkstraksi()
    {
        //additional data
        $this->data_kirim_ekstraksi['operator_ekstraksi'] = $this->faker->name();
        $this->data_kirim_ekstraksi['tanggal_mulai_ekstraksi'] = $this->faker->date();
        $this->data_kirim_ekstraksi['jam_mulai_ekstraksi'] = $this->faker->time();
        $this->data_kirim_ekstraksi['metode_ekstraksi'] = $this->faker->randomElement(MetodeEkstraksiEnum::getValues());
        $this->data_kirim_ekstraksi['nama_kit_ekstraksi'] = $this->faker->name();

        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/kirim-ulang", $this->data_kirim_ekstraksi)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
    }

    public function testKirimUlangEkstraksiNullData()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/kirim-ulang", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testSetKurang()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'sample_invalid']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-kurang/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('pemeriksaansampel', [
            'user_id' => $this->user->id,
            'sampel_id' => $this->sampel->id,
            'kesimpulan_pemeriksaan' => 'sampel kurang',
        ]);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'pcr_sample_analyzed',
            'description' => 'Sample marked as insufficient',
        ]);
    }

    public function testSetKurangWrongSampelStatus()
    {
        $this->sampel = factory(Sampel::class)->create(['sampel_status' => 'extraction_sample_extracted']);
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-kurang/{$this->sampel->id}")
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }

    public function testSwabUlang()
    {
        $data = ['alasan' => $this->faker->paragraph()];
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-swab-ulang/{$this->sampel->id}", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('sampel', ['sampel_status' => 'swab_ulang']);
        $this->assertDatabaseHas('sampel_log', [
            'sampel_status' => 'swab_ulang',
            'description' => 'Sample marked as need to be re-swab',
        ]);
        $this->assertDatabaseHas('pemeriksaansampel', [
            'user_id' => $this->user->id,
            'sampel_id' => $this->sampel->id,
            'kesimpulan_pemeriksaan' => 'swab_ulang',
            'catatan_pemeriksaan' => $data['alasan'],
        ]);
    }

    public function testSwabUlangInvalidSampelId()
    {
        $this->actingAs($this->user)->postJson("/api/v1/ekstraksi/set-swab-ulang/01292")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }
}

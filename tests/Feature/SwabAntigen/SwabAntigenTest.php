<?php

namespace Tests\Feature;

use App\Enums\HasilPeriksaEnum;
use App\Enums\JenisAntigenEnum;
use App\Enums\JenisGejalaEnum;
use App\Enums\JenisIdentitasEnum;
use App\Enums\JenisKelaminEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\KondisiPasienEnum;
use App\Enums\StatusAntigenEnum;
use App\Enums\TujuanPemeriksaanEnum;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\SwabAntigen;
use App\Models\Validator;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SwabAntigenTest extends TestCase
{
    use WithFaker;

    protected $getDataJsonStructure = [
        'data' => [
            "0" => [
                "id",
                "nomor_registrasi",
                "nama_pasien",
                "no_identitas",
                "usia_tahun",
                "tanggal_lahir",
                "kategori",
                "tanggal_periksa",
                "tanggal_validasi",
                "status",
                "no_hasil",
                "hasil_periksa",
                "waktu_sampel_print",
                "counter_print_hasil",
                "validator",
                "kota" => [
                    "id",
                    "provinsi_id",
                    "nama",
                    "provinsi" => [
                        "id",
                        "nama"
                    ],
                ],
                "provinsi" => [
                    "id",
                    "nama",
                ],
                "kecamatan",
                "kelurahan"
            ]
        ],
        "links" => [
            "first",
            "last",
            "prev",
            "next"
        ],
        "meta" => [
            "current_page",
            "from",
            "last_page",
            "path",
            "per_page",
            "to",
            "total",
        ]
    ];

    protected $showDataJsonStructure = [
            "id",
            "nama_pasien",
            "nomor_registrasi",
            "tanggal_lahir",
            "usia_tahun",
            "usia_bulan",
            "jenis_kelamin",
            "no_telp",
            "warganegara",
            "negara_asal",
            "jenis_identitas",
            "no_identitas",
            "kategori",
            "alamat",
            "kode_provinsi",
            "kode_kota_kabupaten",
            "kode_kecamatan",
            "kode_kelurahan",
            "rt",
            "rw",
            "kondisi_pasien",
            "tanggal_gejala",
            "jenis_gejala",
            "tujuan_pemeriksaan",
            "tujuan_pemeriksaan_lainnya",
            "jenis_antigen",
            "no_hasil",
            "tanggal_periksa",
            "hasil_periksa",
            "tanggal_validasi",
            "validator_id",
            "valid_file_id",
            "status",
            "waktu_sampel_print",
            "counter_print_hasil",
            "deleted_at",
            "created_at",
            "updated_at",
            "provinsi" => [
                "id",
                "nama",
            ],
            "kota" => [
                "id",
                "provinsi_id",
                "nama",
                "provinsi" => [
                    "id",
                    "nama",
                ]
            ],
            "kecamatan",
            "kelurahan",
            "validator",
            "logs",
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        Kelurahan::query()->truncate();
        Kecamatan::query()->truncate();
        Kota::query()->truncate();
        Provinsi::query()->truncate();

        $this->kelurahan = factory(Kelurahan::class)->create();
        $this->swab_antigen = factory(SwabAntigen::class)->create();

        $this->data = [
            "alamat" => $this->faker->address(),
            "hasil_periksa" => $this->faker->randomElement(HasilPeriksaEnum::getValues()),
            "jenis_antigen" => $this->faker->randomElement(JenisAntigenEnum::getValues()),
            "jenis_gejala" => $this->faker->randomElement(JenisGejalaEnum::getValues()),
            "jenis_identitas" => $this->faker->randomElement(JenisIdentitasEnum::getValues()),
            "jenis_kelamin" => $this->faker->randomElement(JenisKelaminEnum::getValues()),
            "kategori" => $this->faker->randomNumber(),
            "kode_kecamatan" => $this->kelurahan->kecamatan->id,
            "kode_kelurahan" => $this->kelurahan->id,
            "kode_kota_kabupaten" => $this->kelurahan->kecamatan->kota->id,
            "kode_provinsi" => $this->kelurahan->kecamatan->kota->provinsi->id,
            "kondisi_pasien" => $this->faker->randomElement(KondisiPasienEnum::getValues()),
            "nama_pasien" => $this->faker->name(),
            "negara_asal" => $this->faker->country(),
            "no_hasil" => rand(1000,9999).date('m'),
            "no_identitas" => "1234567890123456",
            "no_telp" => $this->faker->randomNumber(),
            "rt" => rand(1,999),
            "rw" => rand(1,999),
            "tanggal_gejala" => $this->faker->date(),
            "tanggal_lahir" => $this->faker->date(),
            "tanggal_periksa" => $this->faker->date(),
            "tujuan_pemeriksaan" => $this->faker->randomElement(TujuanPemeriksaanEnum::getIndices()),
            "tujuan_pemeriksaan_lainnya" => $this->faker->name(),
            "usia_bulan" => rand(0,11),
            "usia_tahun" => rand(1,60),
            "warganegara" => $this->faker->randomElement(KewarganegaraanEnum::getValues())
        ];
    }

    public function testGetDataHasilPemeriksaan()
    {
        $status = StatusAntigenEnum::registrasi();
        $this->swab_antigen = factory(SwabAntigen::class)->create(['status' => $status]);
        $this->actingAs($this->user)
            ->getJson("/api/v1/swab-antigen?status=$status")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure($this->getDataJsonStructure);
    }

    public function testGetDataHasilTervalidasi()
    {
        $status = StatusAntigenEnum::tervalidasi();
        $this->swab_antigen = factory(SwabAntigen::class)->create(['status' => $status]);
        $this->actingAs($this->user)
            ->getJson("/api/v1/swab-antigen?status=$status")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure($this->getDataJsonStructure);
    }

    public function testShowData()
    {
        $this->actingAs($this->user)->getJson("/api/v1/swab-antigen/{$this->swab_antigen->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure($this->showDataJsonStructure);
    }

    public function testShowDataInvalidId()
    {
        $this->actingAs($this->user)->getJson("/api/v1/swab-antigen/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testStore()
    {
        $this->actingAs($this->user)
            ->postJson("/api/v1/swab-antigen", $this->data)
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('swab_antigen', $this->data);
    }

    public function testUpdate()
    {
        unset($this->data['no_hasil']);
        $this->actingAs($this->user)
            ->putJson("/api/v1/swab-antigen/{$this->swab_antigen->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertDatabaseHas('swab_antigen', $this->data);
    }

    public function testUpdateInvalidId()
    {
        unset($this->data['no_hasil']);
        $this->actingAs($this->user)
            ->putJson("/api/v1/swab-antigen/0", $this->data)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testUpdateNullParams()
    {
        $this->actingAs($this->user)
            ->putJson("/api/v1/swab-antigen/{$this->swab_antigen->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testDestroy()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/v1/swab-antigen/{$this->swab_antigen->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertSoftDeleted('swab_antigen', ['id' => $this->swab_antigen->id]);
    }

    public function testDestroyInvalidId()
    {
        $this->actingAs($this->user)
            ->deleteJson("/api/v1/swab-antigen/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testGetNomorRegistrasi()
    {
        $this->actingAs($this->user)
            ->getJson("/api/v1/swab-antigen/nomor-registrasi")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['nomor_registrasi']);
    }

    public function testBulkValidasi()
    {
        $validator = factory(Validator::class)->create();
        $data = [
            'id' => [$this->swab_antigen->id],
            'validator_id' => $validator->id,
        ];

        $this->actingAs($this->user)
            ->postJson("/api/v1/swab-antigen/bulk", $data)
            ->assertJsonStructure(['message'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('swab_antigen', [
            'validator_id' => $validator->id,
            'status' => StatusAntigenEnum::tervalidasi(),
            'tanggal_validasi' => Carbon::now(),
        ]);
    }

    public function testBulkValidasiInvalidId()
    {
        $validator = factory(Validator::class)->create();
        $data = [
            'id' => 0,
            'validator_id' => $validator->id,
        ];

        $this->actingAs($this->user)
            ->postJson("/api/v1/swab-antigen/bulk", $data)
            ->assertJsonStructure(['message'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testBulkValidasiInvalidValidatorId()
    {
        $data = [
            'id' => [$this->swab_antigen->id],
            'validator_id' => 0,
        ];

        $this->actingAs($this->user)
            ->postJson("/api/v1/swab-antigen/bulk", $data)
            ->assertJsonStructure(['message'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testBulkValidasiNullParams()
    {
        $data = [
            'id' => null,
            'validator_id' => null,
        ];

        $this->actingAs($this->user)
            ->postJson("/api/v1/swab-antigen/bulk", $data)
            ->assertJsonStructure(['message'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testExport()
    {
        $this->actingAs($this->user)->getJson("/api/v1/swab-antigen/export-excel")->assertStatus(Response::HTTP_OK);
    }

    public function testImport()
    {
        $path = storage_path('test/FormatSwabAntigen.xlsx');
        $file = new UploadedFile($path, 'FormatSwabAntigen.xlsx', 'xlsx', null, true);
        $data = ['register_file' => $file];
        $this->actingAs($this->user)
            ->postJson('/api/v1/swab-antigen/import', $data)
            ->assertJsonStructure(['message'])
            ->assertStatus(Response::HTTP_OK);

    }

    public function testImportWrongFormat()
    {
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = ['register_file' => $file];
        $this->actingAs($this->user)
            ->postJson('/api/v1/swab-antigen/import', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message']);
    }
}

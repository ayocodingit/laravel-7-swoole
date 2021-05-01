<?php

namespace Tests\Feature\Kota;

use App\Models\Kota;
use App\Models\Provinsi;
use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class KotaTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Kota::query()->truncate();
        Provinsi::query()->truncate();
        $this->user = factory(User::class)->create();
        $this->kota = factory(Kota::class)->create();
        $this->url = '/api/kota';
        $this->model = 'kota';
    }

    public function testGetData()
    {
        $this->actingAs($this->user)->getJson($this->url)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'provinsi' => [
                            'id',
                            'nama'
                        ],
                        'provinsi_id'
                    ]
                ]
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testSearchData()
    {
        $this->actingAs($this->user)->getJson("{$this->url}?search={$this->kota->nama}")
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'provinsi' => [
                            'id',
                            'nama'
                        ]
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testShowData()
    {
        $this->actingAs($this->user)->getJson("{$this->url}/{$this->kota->id}")
            ->assertSuccessful()
            ->assertJsonStructure([
                'result' => [
                    'id',
                    'nama',
                    'provinsi' => [
                        'id',
                        'nama'
                    ]
                ]
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testShowInvalid()
    {
        $this->actingAs($this->user)->getJson("/api/kota/9999")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error','code']);
    }

    public function testStore()
    {
        $this->data = [
            'id' => 2,
            'nama' => 'TESTDATA',
            'provinsi_id' => $this->kota->provinsi->id

        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas($this->model, $this->data);
    }

    public function testUpdate()
    {
        $this->data = [
            'id' => 2,
            'nama' => 'TESTDATA',
            'provinsi_id' => $this->kota->provinsi->id
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->kota->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertDatabaseHas($this->model, $this->data);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)->deleteJson("{$this->url}/{$this->kota->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
    }

    public function testDeleteInvalid()
    {
        $this->actingAs($this->user)->deleteJson("{$this->url}/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }
}

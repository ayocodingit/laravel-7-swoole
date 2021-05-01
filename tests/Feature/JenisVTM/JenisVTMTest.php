<?php

namespace Tests\Feature\JenisVTM;

use App\Models\JenisVTM;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class JenisVTMTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->jenis_vtm = factory(JenisVTM::class)->create();
    }

    public function testIndex()
    {
        $this->actingAs($this->user)->getJson('/api/v1/jenis-vtm')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function testIndexWithParams()
    {
        $this->actingAs($this->user)->getJson("/api/v1/jenis-vtm?search={$this->jenis_vtm->nama}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function testShow()
    {
        $this->actingAs($this->user)->getJson("/api/v1/jenis-vtm/{$this->jenis_vtm->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'result' => [
                    'id',
                    'nama',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testShowWithInvalidId()
    {
        $this->actingAs($this->user)->getJson("/api/v1/jenis-vtm/9999")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testStore()
    {
        $data = [
            'nama' => 'testData'
        ];
        $this->actingAs($this->user)->postJson("/api/v1/jenis-vtm", $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('jenis_vtm', [
            'nama' => $data['nama']
        ]);
    }

    public function testStoreNullName()
    {
        $data = [
            'nama' => null
        ];
        $this->actingAs($this->user)->postJson("/api/v1/jenis-vtm", $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdate()
    {
        $data = [
            'nama' => $this->jenis_vtm->nama
        ];
        $this->actingAs($this->user)->putJson("/api/v1/jenis-vtm/{$this->jenis_vtm->id}", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertDatabaseHas('jenis_vtm', [
            'nama' => $data['nama']
        ]);
    }

    public function testUpdateNullName()
    {
        $data = [
            'nama' => null
        ];
        $this->actingAs($this->user)->putJson("/api/v1/jenis-vtm/{$this->jenis_vtm->id}", $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)->deleteJson("/api/v1/jenis-vtm/{$this->jenis_vtm->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
    }
}

<?php

namespace Tests\Feature\ReagenEkstraksi;

use App\Enums\MetodeEkstraksiEnum;
use App\Models\ReagenEkstraksi;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReagenEkstraksiTest extends TestCase
{
    public $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->record = factory(ReagenEkstraksi::class)->create();
        $this->model = 'reagen_ekstraksi';
        $this->url = '/api/v1/reagen-ekstraksi';
    }

    public function testIndex()
    {
        $this->actingAs($this->user)->getJson($this->url)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'metode_ekstraksi'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function testIndexWithParams()
    {
        $this->actingAs($this->user)->getJson("{$this->url}?search={$this->record->nama}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '0' => [
                        'id',
                        'nama',
                        'metode_ekstraksi'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function testShow()
    {
        $this->actingAs($this->user)->getJson("{$this->url}/{$this->record->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'result' => [
                    'id',
                    'nama',
                    'metode_ekstraksi'
                ]
            ]);
    }

    public function testShowWithInvalidId()
    {
        $this->actingAs($this->user)->getJson("{$this->url}/9999")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }

    public function testStore()
    {
        $this->data = [
            'nama' => 'testData',
            'metode_ekstraksi' => MetodeEkstraksiEnum::Manual()
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas($this->model, [
            'nama' => $this->data['nama'],
            'metode_ekstraksi' => $this->data['metode_ekstraksi']
        ]);
    }

    public function testStoreNullName()
    {
        $this->data = [
            'nama' => null,
            'metode_ekstraksi' => MetodeEkstraksiEnum::Manual()
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testStoreNullMetodeEkstraksi()
    {
        $this->data = [
            'nama' => 'Data',
            'metode_ekstraksi' => null
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdate()
    {
        $this->data = [
            'nama' => $this->record->nama,
            'metode_ekstraksi' => MetodeEkstraksiEnum::Manual()
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->record->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertDatabaseHas($this->model, [
            'nama' => $this->data['nama'],
            'metode_ekstraksi' => $this->data['metode_ekstraksi']
        ]);
    }

    public function testUpdateNullName()
    {
        $this->data = [
            'nama' => null,
            'metode_ekstraksi' => MetodeEkstraksiEnum::Manual()
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->record->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdateNullMetodeEkstraksi()
    {
        $this->data = [
            'nama' => 'testData',
            'metode_ekstraksi' => null
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->record->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)->deleteJson("{$this->url}/{$this->record->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
    }

    public function testDeleteInvalid()
    {
        $this->actingAs($this->user)->deleteJson("{$this->url}/9999")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }
}

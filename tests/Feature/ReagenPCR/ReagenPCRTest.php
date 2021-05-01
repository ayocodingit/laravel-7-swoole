<?php

namespace Tests\Feature\ReagenEkstraksi;

use App\Enums\MetodeEkstraksiEnum;
use App\Models\ReagenPCR;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReagenPCRTest extends TestCase
{
    use WithFaker;

    public $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->record = factory(ReagenPCR::class)->create();
        $this->model = 'reagen_pcr';
        $this->url = '/api/v1/reagen-pcr';
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
                        'ct_normal'
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
                        'ct_normal'
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
                    'ct_normal'
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
            'ct_normal' => $this->faker->randomNumber()
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas($this->model, $this->data);
    }

    public function testStoreNullName()
    {
        $this->data = [
            'nama' => null,
            'ct_normal' => $this->faker->randomNumber()
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testStoreNullMetodeEkstraksi()
    {
        $this->data = [
            'nama' => 'Data',
            'ct_normal' => null
        ];
        $this->actingAs($this->user)->postJson("{$this->url}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdate()
    {
        $this->data = [
            'nama' => $this->record->nama,
            'ct_normal' => $this->faker->randomNumber()
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->record->id}", $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message']);
        $this->assertDatabaseHas($this->model, $this->data);
    }

    public function testUpdateNullName()
    {
        $this->data = [
            'nama' => null,
            'ct_normal' => $this->faker->randomNumber()
        ];
        $this->actingAs($this->user)->putJson("{$this->url}/{$this->record->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdateNullMetodeEkstraksi()
    {
        $this->data = [
            'nama' => 'testData',
            'ct_normal' => null
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
        $this->assertDatabaseMissing($this->model, $this->record->toArray());
    }

    public function testDeleteInvalid()
    {
        $this->actingAs($this->user)->deleteJson("{$this->url}/9999")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['error', 'code']);
    }
}

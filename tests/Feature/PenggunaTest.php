<?php

namespace Tests\Feature;

use App\Models\Validator;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PenggunaTest extends TestCase
{
    use WithFaker;

    private $responseData = [
        "id",
        "name",
        "username",
        "email",
        "email_verified_at",
        "role_id",
        "created_at",
        "updated_at",
        "lab_pcr_id",
        "validator_id",
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->data = [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'role_id' => factory(Role::class)->create()->id,
            'validator_id' => factory(Validator::class)->create()->id,
            'password' => '123123',
            'password_confirmation' => '123123',
        ];
    }

    public function testList()
    {
        $this->actingAs($this->user)->getJson('/api/pengguna')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseData,
                ],
            ]);
    }

    public function testListWithParams()
    {
        $this->actingAs($this->user)->getJson("/api/pengguna?search={$this->user->name}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "*" => $this->responseData,
                ],
            ]);
    }

    public function testShow()
    {
        $this->actingAs($this->user)->getJson("/api/pengguna/{$this->user->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(["result" => $this->responseData]);
    }

    public function testShowInvalidId()
    {
        $this->actingAs($this->user)->getJson("/api/pengguna/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(["error", "code"]);
    }

    public function testDelete()
    {
        $this->actingAs($this->user)->deleteJson("/api/pengguna/{$this->user->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(["message"]);
    }

    public function testDeleteInvalidId()
    {
        $this->actingAs($this->user)->deleteJson("/api/pengguna/012312")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(["error", "code"]);
    }

    public function testStore()
    {
        $data = $this->data;
        $this->actingAs($this->user)->postJson("/api/pengguna", $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(["message"]);
        unset($data['password_confirmation']);
        unset($data['password']);
        $this->assertDatabaseHas('users', $data);
    }

    public function testStoreNullData()
    {
        $this->actingAs($this->user)->postJson("/api/pengguna", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testStorePasswordUnderEightCharacter()
    {
        $this->data['password'] = '123';
        $this->data['password_confirmation'] = '123';
        $this->actingAs($this->user)->postJson("/api/pengguna", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testStoreExistingUsername()
    {
        $this->data['username'] = $this->user->username;
        $this->actingAs($this->user)->postJson("/api/pengguna", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testStoreExistingEmail()
    {
        $this->data['email'] = $this->user->email;
        $this->actingAs($this->user)->postJson("/api/pengguna", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testStoreInvalidRoleId()
    {
        $this->data['role_id'] = 0;
        $this->actingAs($this->user)->postJson("/api/pengguna", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testStoreInvalidValidatorId()
    {
        $this->data['validator_id'] = 0;
        $this->actingAs($this->user)->postJson("/api/pengguna", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdate()
    {
        $data = $this->data;
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(["message"]);
        unset($data['password_confirmation']);
        unset($data['password']);
        $this->assertDatabaseHas('users', $data);
    }

    public function testUpdateNullData()
    {
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdatePasswordUnderEightCharacter()
    {
        $this->data['password'] = '123';
        $this->data['password_confirmation'] = '123';
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdateExistingUsername()
    {
        $user = factory(User::class)->create();
        $this->data['username'] = $user->username;
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdateExistingEmail()
    {
        $user = factory(User::class)->create();
        $this->data['email'] = $user->email;
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdateInvalidRoleId()
    {
        $this->data['role_id'] = 0;
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }

    public function testUpdateInvalidValidatorId()
    {
        $this->data['validator_id'] = "010";
        $this->actingAs($this->user)->patchJson("/api/pengguna/{$this->user->id}", $this->data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);
    }
}

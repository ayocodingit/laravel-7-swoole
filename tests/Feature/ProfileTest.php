<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testUpdateProfile()
    {
        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];

        $this->actingAs($this->user)->patchJson('/api/settings/profile', $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['id', 'name', 'email', 'username', 'role_id', 'email_verified_at'])
            ->assertJson([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'name' => $data['name'],
        ]);
    }

    public function testUpdateProfileInvalidEmail()
    {
        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->city(),
        ];

        $this->actingAs($this->user)->patchJson('/api/settings/profile', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);

        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
        ]);
    }

    public function testUpdatePassword()
    {
        $data = [
            'password' => '12345679',
            'password_confirmation' => '12345679',
        ];

        $this->actingAs($this->user)->patchJson('/api/settings/password', $data)->assertStatus(Response::HTTP_OK);
    }

    public function testUpdatePasswordWrongConfirmation()
    {
        $data = [
            'password' => '12345679',
            'password_confirmation' => '123123123',
        ];

        $this->actingAs($this->user)->patchJson('/api/settings/password', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testUpdatePasswordUnderEightCharacter()
    {
        $data = [
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $this->actingAs($this->user)->patchJson('/api/settings/password', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }
}

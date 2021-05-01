<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testLogin()
    {
        $data = [
            'username' => $this->user->username,
            'password' => 'password',
        ];

        $this->postJson('/api/login', $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token', 'expires_in', 'user'])
            ->assertJson(['token_type' => 'bearer']);
    }

    public function testLoginInvalidCredential()
    {
        $data = [
            'username' => 'cobasalah',
            'password' => 'asal',
        ];

        $this->postJson('/api/login', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testShowProfile()
    {
        $this->actingAs($this->user)->getJson('/api/user')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['id', 'name', 'email', 'username', 'role_id', 'email_verified_at']);
    }

    public function testShowProfileWithoutLogin()
    {
        $this->getJson('/api/user')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testLogout()
    {
        $data = [
            'username' => $this->user->username,
            'password' => 'password',
        ];

        $token = $this->postJson('/api/login', $data);
        $this->postJson("/api/logout?token={$token['token']}")->assertSuccessful();
        $this->getJson("/api/user?token={$token['token']}")->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}

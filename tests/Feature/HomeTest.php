<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testHomeApi()
    {
        $this->getJson('/api')
                ->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure(['app', 'server']);
    }

    public function testHomeWeb()
    {
        $this->getJson('/')
                ->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure(['app', 'server']);
    }
}

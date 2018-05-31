<?php

namespace Tests\Feature;

use Hashids;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShortenControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShorten(): void
    {
        $url = $this->faker->url;

        $response = $this->post('/api/shorten', ['url' => $url]);
        $response->assertExactJson(['success' => true]);

        $hash = $response->json('hash');
        $this->assertDatabaseHas('shorteners', ['id' => Hashids::decode($hash), 'hash' => $hash, 'url' => $url]);
    }
}

<?php

namespace Tests\Feature;

use Hashids;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ShortenControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Shorten url
     *
     * @return void
     */
    public function testShorten(): void
    {
        $url = $this->faker->url;

        $response = $this->post('/api/shorten', ['url' => $url]);
        $hash = $response->json('hash');

        $response->assertStatus(200)
            ->assertExactJson(['success' => true, 'hash' => $hash]);

        $this->assertDatabaseHas('shorteners', ['id' => Hashids::decode($hash), 'hash' => $hash, 'url' => $url]);
    }

    /**
     * Validation fails
     *
     * @return void
     */
    public function testValidationFails(): void
    {
        $response = $this->post('/api/shorten');

        $response->assertSessionHasErrors(['url']);
    }


    /**
     * Invalid url
     *
     * @return void
     */
    public function testInvalidUrl(): void
    {
        $this->withoutExceptionHandling();

        $cases = ['//invalid-url.com', '/invalid-url', 'invalid-url.com'];

        foreach ($cases as $case) {
            try {
                $this->post('/api/shorten', ['url' => $case]);
            } catch (ValidationException $e) {
                $this->assertEquals(
                    'The url format is invalid.',
                    $e->validator->errors()->first('url')
                );
                continue;
            }

            $this->fail("The URL $case passed validation when it should have failed.");
        }
    }

    /**
     * Validation fail when url is longer that max:20482
     *
     * @return void
     */
    public function testMaxLengthWhenTooLong(): void
    {
        $this->withoutExceptionHandling();

        $url = 'http://';
        $url .= str_repeat('a', 2049 - \strlen($url));

        try {
            $this->post('/api/shorten', ['url' => $url]);
        } catch (ValidationException $e) {
            $this->assertEquals(
                'The url may not be greater than 2048 characters.',
                $e->validator->errors()->first('url')
            );
            return;
        }

        $this->fail('Max length should trigger a ValidationException');
    }

    /**
     * Validation success when url is longer is equal max:2048
     *
     * @return void
     */
    public function testMaxLengthWhenUnderMax(): void
    {
        $url = 'http://';
        $url .= str_repeat('a', 2048 - \strlen($url));

        $this->post('/api/shorten', ['url' => $url]);

        $this->assertDatabaseHas('shorteners', ['url' => $url]);
    }

}

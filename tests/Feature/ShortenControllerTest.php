<?php

namespace Tests\Feature;

use Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use UrlShortener\Shortener;

class ShortenControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Shorten url
     *
     * @return array
     */
    public function testShorten(): array
    {
        $url = $this->faker->url;

        $response = $this->post('/api/shorten', ['url' => $url]);
        $hash = $response->json('hash');

        $response->assertStatus(200)
            ->assertExactJson(['success' => true, 'hash' => $hash]);

        $this->assertDatabaseHas('shorteners', ['id' => Hashids::decode($hash), 'hash' => $hash, 'url' => $url]);
        return compact('hash', 'url');
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


    /**
     * Get url by shorten hash
     * @depends testShorten
     * @param array $params Keys: hash, url
     * @return Shortener
     */
    public function testShow($params): Shortener
    {
        /**
         * @var string $hash
         * @var string $url
         */
        extract($params);
        $response = $this->get("/api/$hash");
        $response->assertStatus(200)
            ->assertExactJson(['success' => true, 'url' => $url]);

        $shortener = Shortener::where('hash', $hash)->first();
        return $shortener;
    }


    /**
     * Get url by shorten invalid hash
     */
    public function testInvalidHashWithoutExceptionHandling(): void
    {
        $this->withoutExceptionHandling();

        $hash = 'a';
        try {
            $this->get("/api/$hash");
        } catch (ModelNotFoundException $e) {
            $this->assertEquals(
                'No query results for model [UrlShortener\Shortener].',
                $e->getMessage()
            );
            return;
        }
        $this->fail('The hash found result when it should have failed.');
    }


    /**
     * Get url by shorten invalid hash, check http status
     */
    public function testInvalidHashPageNotFound404(): void
    {
        $hash = 'a';
        $response = $this->get("/api/$hash");
        $response->assertStatus(404);
    }


    /**
     * First visit save
     * @depends testShow
     * @param Shortener $shortener
     */
    public function testFirstVisit($shortener): void
    {
        $this->assertDatabaseHas('visits', ['shortener_id' => $shortener->id, 'date' => date('Y-m-d'), 'count' => 1]);
    }


    /**
     * Second visit save
     * @depends testShow
     * @param Shortener $shortener
     */
    public function testSecondVisit($shortener): void
    {
        $this->get("/api/{$shortener->id}");
        $this->assertDatabaseHas('visits', ['shortener_id' => $shortener->id, 'date' => date('Y-m-d'), 'count' => 2]);
    }
}
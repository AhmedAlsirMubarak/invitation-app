<?php

namespace Tests\Feature;

use Database\Seeders\LandingContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_root_url_shows_the_landing_page(): void
    {
        $this->seed(LandingContentSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(__('landing.brand'));
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test the landing page returns a successful response and contains key CTAs.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Dompetify')
            ->assertSee('Download / Install App')
            ->assertSee('/download/apps');
    }
}

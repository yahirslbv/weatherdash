<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Cambiamos a 302 porque tu ruta raíz hace un redirect a /home
        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }
}
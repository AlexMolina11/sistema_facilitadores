<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Comprueba que la página principal redirige correctamente
     * hacia la pantalla correspondiente de la aplicación.
     */
    public function test_the_application_redirects_from_the_root_route(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }

    /**
     * Comprueba que la pantalla de inicio de sesión
     * puede mostrarse correctamente.
     */
    public function test_the_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
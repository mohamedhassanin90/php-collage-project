<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_root_route_redirects_to_the_login_page()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
        $this->get('/login')->assertOk();
    }

    public function test_the_dashboard_requires_authentication()
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}

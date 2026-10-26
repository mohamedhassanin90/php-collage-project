<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_create_an_account_and_reach_the_assignment_dashboard(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New Student',
            'email' => 'new-student@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'New Student',
            'email' => 'new-student@example.com',
        ]);
        $this->assertTrue(Hash::check('secure-password', User::where('email', 'new-student@example.com')->value('password')));
    }

    public function test_a_registered_student_can_log_in_and_log_out(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('secure-password'),
        ]);

        $this->post(route('login'), [
            'email' => 'student@example.com',
            'password' => 'secure-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_registration_requires_a_unique_email_and_matching_confirmed_password(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Duplicate',
                'email' => 'existing@example.com',
                'password' => 'secure-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
    }
}

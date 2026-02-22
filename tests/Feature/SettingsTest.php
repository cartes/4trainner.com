<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/settings');

        $response->assertOk();
        $response->assertViewIs('settings.index');
    }

    public function test_profile_information_can_be_updated_via_settings(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/settings', [
                'name' => 'Test User Updated',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(); // Redirects back

        $user->refresh();

        $this->assertSame('Test User Updated', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }
}

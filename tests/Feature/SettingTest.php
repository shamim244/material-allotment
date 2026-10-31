<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create(['role' => 'contractor']);

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('Email Connections');
    }

    public function test_admin_can_update_smtp_settings(): void
    {
        $admin = User::factory()->create(['role' => 'contractor']);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'mail_mailer' => 'smtp',
            'mail_host' => 'custom.smtp.host',
            'mail_port' => 587,
            'mail_username' => 'testuser',
            'mail_password' => 'secret',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'system@test.com',
            'mail_from_name' => 'System Test',
        ]);

        $response->assertSessionHas('success', 'Email Connections updated successfully.');

        $this->assertDatabaseHas('settings', [
            'key' => 'mail_host',
            'value' => 'custom.smtp.host',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'mail_from_address',
            'value' => 'system@test.com',
        ]);
    }
}

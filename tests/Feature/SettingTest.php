<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed settings because the application expects them
        $this->seed(\Database\Seeders\SettingSeeder::class);
        // We need Roles for permission checks
        $roles = ['Admin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    public function test_settings_page_is_displayed_for_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get(route('settings.edit'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Settings/Edit')
            ->has('settings')
        );
    }

    public function test_settings_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'app_name' => 'New App Name',
            'institution_name' => 'New Institution Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'app_name',
            'value' => 'New App Name',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'institution_name',
            'value' => 'New Institution Name',
        ]);
    }
}

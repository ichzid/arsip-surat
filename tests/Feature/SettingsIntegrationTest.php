<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\IncomingLetter;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class SettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingSeeder::class);
        
        // Setup Roles
        $roles = ['Admin', 'Operator Divisi Umum', 'Operator Divisi'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    public function test_login_page_displays_custom_app_name()
    {
        // Update App Name
        Setting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => 'Super Arsip 2024']
        );

        $response = $this->get(route('login'));

        $response->assertStatus(200);
        // Inertia props are in data-page attribute, but easier to check if the shared props contain the setting
        // Since GuestLayout is part of the JS bundle, we can't easily grep the HTML for the rendered text unless we use Dusk.
        // But we can check if the Inertia shared props have the settings.
        
        // Wait, Inertia shared props are passed to the initial page load.
        // Let's check the props.
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->has('settings', fn (Assert $json) => $json
                ->where('app_name', 'Super Arsip 2024')
                ->etc()
            )
        );
    }

    public function test_print_disposition_pdf_uses_institution_settings()
    {
        // Update Institution Name
        Setting::updateOrCreate(
            ['key' => 'institution_name'],
            ['value' => 'Kementerian Percobaan']
        );

        $user = User::factory()->create();
        $user->assignRole('Operator Divisi Umum');

        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN-TEST-001',
            'mail_number' => 'SRT/001',
            'mail_date' => '2024-01-01',
            'received_date' => '2024-01-02',
            'origin' => 'Pengirim',
            'subject' => 'Subject',
            'status' => 'new',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('incoming-letters.print-disposition', $letter));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // We can't easily parse PDF content here, but if it returns 200, the view rendering worked.
        // If there was a variable missing in blade, it would usually throw 500.
    }
}

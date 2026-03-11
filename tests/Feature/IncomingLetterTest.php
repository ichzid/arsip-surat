<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\IncomingLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IncomingLetterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup basic roles and divisions
        $this->seedRolesAndDivisions();
    }

    private function seedRolesAndDivisions()
    {
        // Roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Operator Divisi Umum']);
        Role::firstOrCreate(['name' => 'Operator Divisi']);
        
        // Divisions
        Division::create(['name' => 'Divisi Umum', 'code' => 'UMUM']);
        Division::create(['name' => 'Divisi IT', 'code' => 'IT']);
    }

    public function test_operator_divisi_umum_can_view_incoming_letters_page()
    {
        $division = Division::where('code', 'UMUM')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi Umum');

        $response = $this->actingAs($user)->get(route('incoming-letters.index'));

        $response->assertStatus(200);
    }

    public function test_operator_divisi_cannot_create_incoming_letter()
    {
        $division = Division::where('code', 'IT')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi');

        $response = $this->actingAs($user)->get(route('incoming-letters.create'));

        $response->assertStatus(403); // Forbidden
    }

    public function test_store_incoming_letter_auto_fills_date_if_empty()
    {
        $division = Division::where('code', 'UMUM')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi Umum');

        $data = [
            'mail_number' => 'TEST/001',
            'origin' => 'Test Origin',
            'subject' => 'Test Subject',
            // mail_date and received_date are omitted
        ];

        $response = $this->actingAs($user)->post(route('incoming-letters.store'), $data);

        $response->assertRedirect(route('incoming-letters.index'));
        
        $letter = IncomingLetter::where('mail_number', 'TEST/001')->first();
        $this->assertNotNull($letter);
        $this->assertEquals(now()->toDateString(), $letter->mail_date->toDateString());
        $this->assertEquals(now()->toDateString(), $letter->received_date->toDateString());
    }

    public function test_store_incoming_letter_uses_provided_dates()
    {
        $division = Division::where('code', 'UMUM')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi Umum');

        $customDate = '2025-01-01';

        $data = [
            'mail_number' => 'TEST/002',
            'origin' => 'Test Origin',
            'subject' => 'Test Subject',
            'mail_date' => $customDate,
            'received_date' => $customDate,
        ];

        $response = $this->actingAs($user)->post(route('incoming-letters.store'), $data);

        $response->assertRedirect(route('incoming-letters.index'));
        
        $letter = IncomingLetter::where('mail_number', 'TEST/002')->first();
        $this->assertNotNull($letter);
        $this->assertEquals($customDate, $letter->mail_date->toDateString());
        $this->assertEquals($customDate, $letter->received_date->toDateString());
    }
}

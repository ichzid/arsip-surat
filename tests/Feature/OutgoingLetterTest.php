<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\OutgoingLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OutgoingLetterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndDivisions();
    }

    private function seedRolesAndDivisions()
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Operator Divisi']);
        Role::firstOrCreate(['name' => 'Pimpinan']);

        Division::create(['name' => 'Divisi IT', 'code' => 'IT']);
        Division::create(['name' => 'Divisi Keuangan', 'code' => 'KEU']);
    }

    public function test_operator_can_create_outgoing_letter()
    {
        $division = Division::where('code', 'IT')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi');

        $data = [
            'mail_number' => 'OUT/IT/001',
            'recipient' => 'External Vendor',
            'subject' => 'Project Proposal',
            'mail_date' => '2025-01-01',
        ];

        $response = $this->actingAs($user)->post(route('outgoing-letters.store'), $data);

        $response->assertRedirect(route('outgoing-letters.index'));
        $this->assertDatabaseHas('outgoing_letters', [
            'mail_number' => 'OUT/IT/001',
            'division_id' => $division->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_operator_cannot_see_other_division_outgoing_letters()
    {
        // IT User & Letter
        $itDiv = Division::where('code', 'IT')->first();
        $itUser = User::factory()->create(['division_id' => $itDiv->id]);
        $itUser->assignRole('Operator Divisi');

        OutgoingLetter::create([
            'mail_number' => 'IT-MAIL',
            'recipient' => 'A',
            'subject' => 'A',
            'mail_date' => now(),
            'division_id' => $itDiv->id,
            'created_by' => $itUser->id
        ]);

        // Keuangan User & Letter
        $keuDiv = Division::where('code', 'KEU')->first();
        $keuUser = User::factory()->create(['division_id' => $keuDiv->id]);
        $keuUser->assignRole('Operator Divisi');

        OutgoingLetter::create([
            'mail_number' => 'KEU-MAIL',
            'recipient' => 'B',
            'subject' => 'B',
            'mail_date' => now(),
            'division_id' => $keuDiv->id,
            'created_by' => $keuUser->id
        ]);

        // Act: Keuangan User views index
        $response = $this->actingAs($keuUser)->get(route('outgoing-letters.index'));

        // Assert: Should see KEU-MAIL but NOT IT-MAIL
        $response->assertSee('KEU-MAIL');
        $response->assertDontSee('IT-MAIL');
    }

    public function test_admin_can_see_all_outgoing_letters()
    {
        $itDiv = Division::where('code', 'IT')->first();
        $keuDiv = Division::where('code', 'KEU')->first();
        $admin = User::factory()->create(['division_id' => $itDiv->id]); // Div doesn't matter for admin
        $admin->assignRole('Admin');

        OutgoingLetter::create([
            'mail_number' => 'IT-MAIL',
            'recipient' => 'A',
            'subject' => 'A',
            'mail_date' => now(),
            'division_id' => $itDiv->id,
            'created_by' => $admin->id
        ]);

        OutgoingLetter::create([
            'mail_number' => 'KEU-MAIL',
            'recipient' => 'B',
            'subject' => 'B',
            'mail_date' => now(),
            'division_id' => $keuDiv->id,
            'created_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->get(route('outgoing-letters.index'));

        $response->assertSee('IT-MAIL');
        $response->assertSee('KEU-MAIL');
    }
}

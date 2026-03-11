<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\IncomingLetter;
use App\Models\Disposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class DispositionTest extends TestCase
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
        Role::firstOrCreate(['name' => 'Operator Divisi Umum']);
        Role::firstOrCreate(['name' => 'Operator Divisi']);
        
        Division::create(['name' => 'Divisi Umum', 'code' => 'UMUM']);
        Division::create(['name' => 'Divisi IT', 'code' => 'IT']);
        Division::create(['name' => 'Divisi Keuangan', 'code' => 'KEU']);
    }

    public function test_privileged_user_sees_all_dispositions()
    {
        $umumDiv = Division::where('code', 'UMUM')->first();
        $itDiv = Division::where('code', 'IT')->first();
        $keuDiv = Division::where('code', 'KEU')->first();

        $umumUser = User::factory()->create(['division_id' => $umumDiv->id]);
        $umumUser->assignRole('Operator Divisi Umum');

        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN/TEST/001',
            'mail_number' => 'TEST/001',
            'mail_date' => now(),
            'received_date' => now(),
            'origin' => 'X',
            'subject' => 'X',
            'created_by' => $umumUser->id
        ]);

        // Disposition 1: Umum -> IT
        Disposition::create([
            'incoming_letter_id' => $letter->id,
            'from_division_id' => $umumDiv->id,
            'to_division_id' => $itDiv->id,
            'notes' => 'For IT',
            'status' => 'pending',
            'due_date' => now()->addDays(2),
            'created_by' => $umumUser->id
        ]);

        // Disposition 2: Umum -> Keuangan
        Disposition::create([
            'incoming_letter_id' => $letter->id,
            'from_division_id' => $umumDiv->id,
            'to_division_id' => $keuDiv->id,
            'notes' => 'For Keuangan',
            'status' => 'pending',
            'due_date' => now()->addDays(2),
            'created_by' => $umumUser->id
        ]);

        $response = $this->actingAs($umumUser)->get(route('incoming-letters.show', $letter->id));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('IncomingLetters/Show')
            ->has('letter.dispositions', 2)
        );
    }

    public function test_regular_division_user_sees_only_relevant_dispositions()
    {
        $umumDiv = Division::where('code', 'UMUM')->first();
        $itDiv = Division::where('code', 'IT')->first();
        $keuDiv = Division::where('code', 'KEU')->first();

        // Create IT User
        $itUser = User::factory()->create(['division_id' => $itDiv->id]);
        $itUser->assignRole('Operator Divisi');

        // Create Letter (created by Umum)
        $umumUser = User::factory()->create(['division_id' => $umumDiv->id]);
        $umumUser->assignRole('Operator Divisi Umum');

        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN/TEST/002',
            'mail_number' => 'TEST/002',
            'mail_date' => now(),
            'received_date' => now(),
            'origin' => 'X',
            'subject' => 'X',
            'created_by' => $umumUser->id
        ]);

        // Disposition 1: Umum -> IT (Should be visible to IT user)
        Disposition::create([
            'incoming_letter_id' => $letter->id,
            'from_division_id' => $umumDiv->id,
            'to_division_id' => $itDiv->id,
            'notes' => 'For IT',
            'status' => 'pending',
            'due_date' => now(),
            'created_by' => $umumUser->id
        ]);

        // Disposition 2: Umum -> Keuangan (Should NOT be visible to IT user)
        Disposition::create([
            'incoming_letter_id' => $letter->id,
            'from_division_id' => $umumDiv->id,
            'to_division_id' => $keuDiv->id,
            'notes' => 'For Keuangan',
            'status' => 'pending',
            'due_date' => now(),
            'created_by' => $umumUser->id
        ]);

        $response = $this->actingAs($itUser)->get(route('incoming-letters.show', $letter->id));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('IncomingLetters/Show')
            ->has('letter.dispositions', 1)
            ->where('letter.dispositions.0.to_division_id', $itDiv->id)
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\IncomingLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrintDispositionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndDivisions();
    }

    private function seedRolesAndDivisions()
    {
        Role::firstOrCreate(['name' => 'Operator Divisi Umum']);
        Division::create(['name' => 'Divisi Umum', 'code' => 'UMUM']);
    }

    public function test_can_generate_disposition_pdf()
    {
        $division = Division::where('code', 'UMUM')->first();
        $user = User::factory()->create(['division_id' => $division->id]);
        $user->assignRole('Operator Divisi Umum');

        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN/TEST/001',
            'mail_number' => 'TEST/001',
            'mail_date' => now(),
            'received_date' => now(),
            'origin' => 'Test Origin',
            'subject' => 'Test Subject',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('incoming-letters.print-disposition', $letter->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

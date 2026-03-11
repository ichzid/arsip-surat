<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ReportTest extends TestCase
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
    }

    public function test_admin_sees_division_filters()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/Index')
            ->has('divisions', 2) // Should see all divisions
        );
    }

    public function test_operator_divisi_sees_no_division_filters()
    {
        $div = Division::where('code', 'IT')->first();
        $user = User::factory()->create(['division_id' => $div->id]);
        $user->assignRole('Operator Divisi');

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/Index')
            ->has('divisions', 0) // Should NOT see division filter options
        );
    }

    public function test_operator_umum_filter_visibility()
    {
        $div = Division::where('code', 'UMUM')->first();
        $user = User::factory()->create(['division_id' => $div->id]);
        $user->assignRole('Operator Divisi Umum');

        // Case 1: Incoming Letters (Should see filters)
        $response = $this->actingAs($user)->get(route('reports.index', ['type' => 'incoming']));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/Index')
            ->has('divisions', 2)
        );

        // Case 2: Outgoing Letters (Should NOT see filters, only own data)
        $response = $this->actingAs($user)->get(route('reports.index', ['type' => 'outgoing']));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/Index')
            ->has('divisions', 0)
        );
    }
}

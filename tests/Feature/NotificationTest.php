<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\IncomingLetter;
use App\Models\User;
use App\Notifications\NewDispositionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles if they don't exist
        if (!Role::where('name', 'Admin')->exists()) Role::create(['name' => 'Admin']);
        if (!Role::where('name', 'Pimpinan')->exists()) Role::create(['name' => 'Pimpinan']);
        if (!Role::where('name', 'Operator Divisi Umum')->exists()) Role::create(['name' => 'Operator Divisi Umum']);
        if (!Role::where('name', 'Operator Divisi')->exists()) Role::create(['name' => 'Operator Divisi']);
    }

    public function test_notification_is_sent_when_disposition_is_created()
    {
        Notification::fake();

        // Create Divisions
        $umumDiv = Division::create(['name' => 'Umum', 'code' => 'UMUM']);
        $itDiv = Division::create(['name' => 'IT', 'code' => 'IT']);

        // Create Users
        $umumUser = User::factory()->create(['division_id' => $umumDiv->id]);
        $umumUser->assignRole('Operator Divisi Umum');

        $itUser = User::factory()->create(['division_id' => $itDiv->id]);
        $itUser->assignRole('Operator Divisi');

        // Create Letter
        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN/TEST/001',
            'mail_number' => 'TEST/001',
            'mail_date' => now(),
            'received_date' => now(),
            'origin' => 'Test Origin',
            'subject' => 'Test Subject',
            'created_by' => $umumUser->id,
            'status' => 'new'
        ]);

        // Login as Umum User
        $this->actingAs($umumUser);

        // Store Disposition
        $response = $this->post(route('dispositions.store', $letter->id), [
            'to_division_id' => $itDiv->id,
            'notes' => 'Please check',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect();

        // Assert Notification Sent
        Notification::assertSentTo(
            [$itUser],
            NewDispositionNotification::class
        );
    }

    public function test_notification_can_be_marked_as_read()
    {
        // Create Divisions
        $umumDiv = Division::create(['name' => 'Umum', 'code' => 'UMUM']);
        $itDiv = Division::create(['name' => 'IT', 'code' => 'IT']);

        // Create Users
        $umumUser = User::factory()->create(['division_id' => $umumDiv->id]);
        $umumUser->assignRole('Operator Divisi Umum');

        $itUser = User::factory()->create(['division_id' => $itDiv->id]);
        $itUser->assignRole('Operator Divisi');

        // Create Letter
        $letter = IncomingLetter::create([
            'agenda_number' => 'AGN/TEST/002',
            'mail_number' => 'TEST/002',
            'mail_date' => now(),
            'received_date' => now(),
            'origin' => 'Test Origin',
            'subject' => 'Test Subject',
            'created_by' => $umumUser->id,
            'status' => 'new'
        ]);

        // Create Disposition Manually to trigger notification logic? 
        // Or just create notification manually for testing mark as read
        
        $this->actingAs($umumUser);
        $this->post(route('dispositions.store', $letter->id), [
            'to_division_id' => $itDiv->id,
            'notes' => 'Please check',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        // Login as IT User
        $this->actingAs($itUser);

        // Check unread notification count
        $this->assertEquals(1, $itUser->unreadNotifications->count());
        $notification = $itUser->unreadNotifications->first();

        // Mark as read
        $response = $this->post(route('notifications.read', $notification->id));

        $response->assertRedirect(route('incoming-letters.show', $letter->id));
        
        // Refresh user to check notifications
        $itUser->refresh();
        $this->assertEquals(0, $itUser->unreadNotifications->count());
    }

    public function test_mark_all_as_read()
    {
         // Create Divisions
         $umumDiv = Division::create(['name' => 'Umum', 'code' => 'UMUM']);
         $itDiv = Division::create(['name' => 'IT', 'code' => 'IT']);
 
         // Create Users
         $umumUser = User::factory()->create(['division_id' => $umumDiv->id]);
         $umumUser->assignRole('Operator Divisi Umum');
 
         $itUser = User::factory()->create(['division_id' => $itDiv->id]);
         $itUser->assignRole('Operator Divisi');
 
         // Create Letter
         $letter = IncomingLetter::create([
             'agenda_number' => 'AGN/TEST/003',
             'mail_number' => 'TEST/003',
             'mail_date' => now(),
             'received_date' => now(),
             'origin' => 'Test Origin',
             'subject' => 'Test Subject',
             'created_by' => $umumUser->id,
             'status' => 'new'
         ]);
 
         $this->actingAs($umumUser);
         // Create 2 dispositions to same division (IT)
         $this->post(route('dispositions.store', $letter->id), [
             'to_division_id' => $itDiv->id,
             'notes' => 'Note 1',
             'due_date' => now()->toDateString(),
         ]);
         
         $this->post(route('dispositions.store', $letter->id), [
            'to_division_id' => $itDiv->id,
            'notes' => 'Note 2',
            'due_date' => now()->toDateString(),
        ]);
 
         // Login as IT User
         $this->actingAs($itUser);
 
         // Check unread notification count
         $this->assertEquals(2, $itUser->unreadNotifications->count());
 
         // Mark all as read
         $response = $this->post(route('notifications.read-all'));
 
         $response->assertRedirect();
         
         // Refresh user to check notifications
         $itUser->refresh();
         $this->assertEquals(0, $itUser->unreadNotifications->count());
    }
}

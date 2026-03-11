<?php

namespace App\Notifications;

use App\Models\Disposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewDispositionNotification extends Notification
{
    use Queueable;

    public $disposition;

    /**
     * Create a new notification instance.
     */
    public function __construct(Disposition $disposition)
    {
        $this->disposition = $disposition;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Load relationships if not already loaded to ensure we have data
        $this->disposition->loadMissing(['letter', 'fromDivision', 'toDivision']);

        return [
            'disposition_id' => $this->disposition->id,
            'letter_id' => $this->disposition->incoming_letter_id,
            'agenda_number' => $this->disposition->letter->agenda_number,
            'mail_number' => $this->disposition->letter->mail_number,
            'subject' => $this->disposition->letter->subject,
            'from_division' => $this->disposition->fromDivision->name,
            'notes' => $this->disposition->notes,
            'url' => route('incoming-letters.show', $this->disposition->incoming_letter_id),
        ];
    }
}

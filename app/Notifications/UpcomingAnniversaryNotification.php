<?php

namespace App\Notifications;

use App\Models\Marriage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingAnniversaryNotification extends Notification
{
    use Queueable;

    protected $marriage;

    public function __construct(Marriage $marriage)
    {
        $this->marriage = $marriage;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntil = now()->diffInDays($this->marriage->marriage_date->setYear(now()->year), false);
        $daysText = $daysUntil == 0 ? 'today' : "in {$daysUntil} days";
        $couple = $this->marriage->husband->full_name . ' & ' . $this->marriage->wife->full_name;

        return (new MailMessage)
                    ->subject('Upcoming Anniversary: ' . $couple)
                    ->line('**' . $couple . '** have their wedding anniversary ' . $daysText . '!')
                    ->line('Date: ' . $this->marriage->marriage_date->format('F j'))
                    ->action('View Marriage', route('members.show', $this->marriage->husband_id))
                    ->line('Celebrate with them!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'marriage_id' => $this->marriage->id,
            'couple' => $this->marriage->husband->full_name . ' & ' . $this->marriage->wife->full_name,
            'type' => 'anniversary',
            'date' => $this->marriage->marriage_date->format('F j'),
        ];
    }
}

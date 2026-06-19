<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Channels\FcmChannel;

class DeathAnniversaryNotification extends Notification
{
    use Queueable;

    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntil = now()->diffInDays($this->member->date_of_death->setYear(now()->year), false);
        $daysText = $daysUntil == 0 ? 'today' : "in {$daysUntil} days";

        return (new MailMessage)
                    ->subject('Remembering ' . $this->member->full_name)
                    ->line('The death anniversary of **' . $this->member->full_name . '** is ' . $daysText . '.')
                    ->line('Date: ' . $this->member->date_of_death->format('F j, Y'))
                    ->action('View Profile', route('members.show', $this->member->id))
                    ->line('May their memory be a blessing.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'member_id' => $this->member->id,
            'member_name' => $this->member->full_name,
            'type' => 'death_anniversary',
            'date' => $this->member->date_of_death->format('F j, Y'),
        ];
    }

    public function toFcm(object $notifiable): array
    {
        $daysUntil = now()->diffInDays($this->member->date_of_death->setYear(now()->year), false);
        $daysText = $daysUntil == 0 ? 'leo!' : "baada ya siku {$daysUntil}!";

        return [
            'title' => 'Kumbukumbu ya Kufariki 🕊️',
            'body' => "Kumbukumbu ya kufariki kwa marehemu " . $this->member->full_name . " ni " . $daysText . " (" . $this->member->date_of_death->format('F d') . "). Tumkumbuke kwa sala.",
            'data' => [
                'click_action' => route('members.show', $this->member->id),
                'member_id' => (string) $this->member->id,
            ]
        ];
    }
}

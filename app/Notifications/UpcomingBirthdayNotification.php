<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Channels\FcmChannel;

class UpcomingBirthdayNotification extends Notification
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
        $daysUntil = now()->diffInDays($this->member->date_of_birth->setYear(now()->year), false);
        $daysText = $daysUntil == 0 ? 'today' : "in {$daysUntil} days";

        return (new MailMessage)
                    ->subject('Upcoming Birthday: ' . $this->member->full_name)
                    ->line('Don\'t forget! **' . $this->member->full_name . '** has a birthday ' . $daysText . '.')
                    ->line('Date: ' . $this->member->date_of_birth->format('F j'))
                    ->action('View Profile', route('members.show', $this->member->id))
                    ->line('Make sure to wish them well!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'member_id' => $this->member->id,
            'member_name' => $this->member->full_name,
            'type' => 'birthday',
            'date' => $this->member->date_of_birth->format('F j'),
        ];
    }

    public function toFcm(object $notifiable): array
    {
        $daysUntil = now()->diffInDays($this->member->date_of_birth->setYear(now()->year), false);
        $daysText = $daysUntil == 0 ? 'leo!' : "baada ya siku {$daysUntil}!";

        return [
            'title' => 'Siku ya Kuzaliwa Inakaribia! 🎉',
            'body' => "Usisahau! Siku ya kuzaliwa ya " . $this->member->full_name . " ni " . $daysText . " (" . $this->member->date_of_birth->format('F d') . ").",
            'data' => [
                'click_action' => route('members.show', $this->member->id),
                'member_id' => (string) $this->member->id,
            ]
        ];
    }
}

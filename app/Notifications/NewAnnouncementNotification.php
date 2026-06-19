<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\FcmChannel;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'type' => 'announcement',
            'content_type' => $this->announcement->type, // e.g. info, warning, danger
        ];
    }

    /**
     * Get the Firebase Cloud Messaging representation.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Tangazo Jipya: ' . $this->announcement->title . ' 📢',
            'body' => strip_tags(substr($this->announcement->content, 0, 150)),
            'data' => [
                'click_action' => route('announcements.index'),
                'announcement_id' => (string) $this->announcement->id,
            ]
        ];
    }
}

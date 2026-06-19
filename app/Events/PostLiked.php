<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLiked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $likesCount;
    public $likedUserId;

    public function __construct(Post $post, $likesCount, $likedUserId)
    {
        $this->post = $post;
        $this->likesCount = $likesCount;
        $this->likedUserId = $likedUserId;
    }

    public function broadcastOn()
    {
        return [
            new Channel('posts'),
        ];
    }
    
    public function broadcastWith()
    {
        return [
            'post_id' => $this->post->id,
            'likes_count' => $this->likesCount,
            'liked_user_id' => $this->likedUserId,
        ];
    }
}

<?php

namespace App\Events;

use App\Models\PostComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $postId;
    public $commentsCount;
    public $userName;

    public function __construct(PostComment $comment, $postId, $commentsCount, $userName)
    {
        $this->comment = $comment;
        $this->postId = $postId;
        $this->commentsCount = $commentsCount;
        $this->userName = $userName;
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
            'post_id' => $this->postId,
            'comments_count' => $this->commentsCount,
            'comment' => [
                'id' => $this->comment->id,
                'parent_id' => $this->comment->parent_id,
                'user_name' => $this->userName,
                'content' => $this->comment->content,
                'time' => $this->comment->created_at->diffForHumans(),
            ]
        ];
    }
}

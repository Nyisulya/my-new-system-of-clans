<?php

namespace App\Http\Controllers\Web;

use App\Events\CommentAdded;
use App\Events\PostLiked;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\ImageService;

class PostController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        // Load only top-level comments and their replies
        $posts = Post::with(['user.member', 'likes', 'comments' => function($q) {
            $q->whereNull('parent_id')->with('replies.user.member', 'user.member');
        }])
            ->latest()
            ->paginate(15);
            
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required_without:image|string|max:10000',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $result = $this->imageService->uploadProfilePhoto($request->file('image'), 'posts');
            $imagePath = $result['path'];
        }

        Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'Post yako imetumwa kikamilifu!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Huruhusiwi kufuta post hii.');
        }

        if ($post->image_path) {
            $this->imageService->deleteImage($post->image_path);
        }

        $post->delete();

        return back()->with('success', 'Post imefutwa.');
    }

    public function toggleLike(Request $request, Post $post)
    {
        $like = $post->likes()->where('user_id', auth()->id())->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => auth()->id()]);
            $liked = true;
        }

        $likesCount = $post->likes()->count();

        // Broadcast Event safely
        try {
            broadcast(new PostLiked($post, $likesCount, auth()->id()))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Pusher Broadcast Failed: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount
            ]);
        }

        return back();
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $comment->load('user.member');
        
        $userName = $comment->user->member ? $comment->user->member->first_name . ' ' . $comment->user->member->last_name : $comment->user->name;
        $commentsCount = $post->comments()->count();

        // Broadcast Event safely
        try {
            broadcast(new CommentAdded($comment, $post->id, $commentsCount, $userName))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Pusher Broadcast Failed: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => $comment->parent_id,
                    'user_name' => $userName,
                    'content' => $comment->content,
                    'time' => $comment->created_at->diffForHumans()
                ],
                'comments_count' => $commentsCount
            ]);
        }

        return back()->with('success', 'Comment imetumwa.');
    }
}

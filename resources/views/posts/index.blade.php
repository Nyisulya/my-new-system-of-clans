@extends('layouts.app')

@section('title', 'Historia / Timeline')

@section('content_header')
    <h1><i class="fas fa-stream text-primary"></i> Historia / Timeline</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        
        <!-- Create Post -->
        <div class="card card-outline card-primary mb-4">
            <div class="card-body">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="Andika historia, tangazo, au jambo lolote hapa..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image"><i class="fas fa-image"></i> Weka Picha (Si lazima)</label>
                        <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Tuma Post</button>
                </form>
            </div>
        </div>

        <!-- Feed -->
        @forelse($posts as $post)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            @if($post->user->member && $post->user->member->profile_photo_path)
                                <img src="{{ asset('storage/' . $post->user->member->profile_photo_path) }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">
                                @if($post->user->member)
                                    <a href="{{ route('members.dashboard', $post->user->member->id) }}">{{ $post->user->member->full_name }}</a>
                                @else
                                    {{ $post->user->name }}
                                @endif
                            </h6>
                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    @if(auth()->id() === $post->user_id || auth()->user()->isAdmin())
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Futa post hii?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>

                <div class="card-body">
                    @if(strlen($post->content) > 200)
                        <p class="card-text post-text-short" id="post-text-short-{{ $post->id }}" style="white-space: pre-wrap;">{{ Str::limit($post->content, 200) }} <a href="javascript:void(0);" onclick="$('#post-text-short-{{ $post->id }}').hide(); $('#post-text-full-{{ $post->id }}').fadeIn();" class="text-primary font-weight-bold" style="text-decoration: none;">Soma zaidi...</a></p>
                        <p class="card-text post-text-full" id="post-text-full-{{ $post->id }}" style="white-space: pre-wrap; display: none;">{{ $post->content }} <a href="javascript:void(0);" onclick="$('#post-text-full-{{ $post->id }}').hide(); $('#post-text-short-{{ $post->id }}').fadeIn();" class="text-muted font-weight-bold" style="text-decoration: none;">Ficha</a></p>
                    @else
                        <p class="card-text" style="white-space: pre-wrap;">{{ $post->content }}</p>
                    @endif
                    
                    @if($post->image_path)
                        <img src="{{ asset('storage/' . $post->image_path) }}" class="img-fluid rounded mb-3" alt="Post Image">
                    @endif
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted text-sm"><i class="fas fa-thumbs-up text-primary"></i> <span id="like-count-{{ $post->id }}">{{ $post->likes->count() }}</span> Likes</span>
                        <span class="text-muted text-sm"><span id="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</span> Comments</span>
                    </div>

                    <div class="d-flex mb-3">
                        <form action="{{ route('posts.like', $post) }}" method="POST" class="mr-2 flex-fill text-center form-like" data-post-id="{{ $post->id }}">
                            @csrf
                            <button type="submit" id="btn-like-{{ $post->id }}" class="btn btn-block btn-sm font-weight-bold {{ $post->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-light text-muted' }}" style="border-radius: 20px;">
                                <i class="fas fa-thumbs-up"></i> <span class="like-text">{{ $post->isLikedBy(auth()->user()) ? 'Umelike' : 'Like' }}</span>
                            </button>
                        </form>
                        <button type="button" class="btn btn-block btn-light btn-sm text-muted font-weight-bold mt-0" style="border-radius: 20px;" onclick="$('#comments-section-{{ $post->id }}').toggle(); $('#commentInput-{{ $post->id }}').focus();">
                            <i class="fas fa-comment"></i> Comment
                        </button>
                    </div>

                    <div id="comments-section-{{ $post->id }}" style="display: none;">
                        <!-- Comments List -->
                        <div id="comments-list-{{ $post->id }}" class="mt-2">
                        @foreach($post->comments as $comment)
                            <div class="d-flex mb-2">
                                <div class="mr-2 mt-1">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="bg-light p-2" style="border-radius: 18px; display: inline-block;">
                                        <strong class="d-block text-sm" style="line-height: 1.2;">
                                            @if($comment->user->member)
                                                {{ $comment->user->member->first_name }} {{ $comment->user->member->last_name }}
                                            @else
                                                {{ $comment->user->name }}
                                            @endif
                                        </strong>
                                        <span class="text-sm" style="line-height: 1.2;">{{ $comment->content }}</span>
                                    </div>
                                    <div class="text-muted ml-2 mt-1" style="font-size: 11px;">
                                        {{ $comment->created_at->diffForHumans() }} &middot; 
                                        <a href="javascript:void(0);" onclick="$('#replyForm-{{ $comment->id }}').toggle(); $('#replyInput-{{ $comment->id }}').focus();" class="text-muted font-weight-bold" style="text-decoration: none;">Reply</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Replies -->
                            <div class="ml-5" id="replies-list-{{ $comment->id }}">
                                @foreach($comment->replies as $reply)
                                <div class="d-flex mb-2">
                                    <div class="mr-2 mt-1">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px;">
                                            <i class="fas fa-user" style="font-size: 10px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="bg-light p-2" style="border-radius: 18px; display: inline-block;">
                                            <strong class="d-block text-sm" style="line-height: 1.2;">
                                                @if($reply->user->member)
                                                    {{ $reply->user->member->first_name }} {{ $reply->user->member->last_name }}
                                                @else
                                                    {{ $reply->user->name }}
                                                @endif
                                            </strong>
                                            <span class="text-sm" style="line-height: 1.2;">{{ $reply->content }}</span>
                                        </div>
                                        <div class="text-muted ml-2 mt-1" style="font-size: 11px;">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Reply Form -->
                                <form action="{{ route('posts.comment', $post) }}" method="POST" class="form-reply mt-2 mb-3" data-post-id="{{ $post->id }}" data-parent-id="{{ $comment->id }}" id="replyForm-{{ $comment->id }}" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px;">
                                                <i class="fas fa-user" style="font-size: 10px;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 position-relative">
                                            <input type="text" name="content" id="replyInput-{{ $comment->id }}" class="form-control form-control-sm" placeholder="Jibu hapa..." style="border-radius: 20px; padding-right: 35px;" required>
                                            <button type="submit" class="btn btn-sm text-primary position-absolute" style="right: 2px; top: 0px; background: transparent; border: none;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <!-- Comment Form -->
                    <form action="{{ route('posts.comment', $post) }}" method="POST" class="form-comment mt-3 border-top pt-3" data-post-id="{{ $post->id }}" id="commentForm-{{ $post->id }}">
                        @csrf
                        <div class="d-flex align-items-center">
                            <div class="mr-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 position-relative">
                                <input type="text" name="content" id="commentInput-{{ $post->id }}" class="form-control" placeholder="Andika comment..." style="border-radius: 20px; padding-right: 40px;" required>
                                <button type="submit" class="btn btn-sm text-primary position-absolute" style="right: 5px; top: 3px; background: transparent; border: none;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                Hakuna post yoyote bado. Kuwa wa kwanza kuandika historia hapa!
            </div>
        @endforelse

        {{ $posts->links() }}
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // AJAX for Like
    $('.form-like').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let postId = form.data('post-id');
        let url = form.attr('action');
        let btn = $('#btn-like-' + postId);
        let likeText = btn.find('.like-text');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function(response) {
                if(response.liked) {
                    btn.removeClass('btn-light text-muted').addClass('btn-primary');
                    likeText.text('Umelike');
                } else {
                    btn.removeClass('btn-primary').addClass('btn-light text-muted');
                    likeText.text('Like');
                }
                $('#like-count-' + postId).text(response.likes_count);
            }
        });
    });

    // AJAX for Comment
    $('.form-comment').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let postId = form.data('post-id');
        let url = form.attr('action');
        let input = $('#commentInput-' + postId);
        
        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    // Update count
                    $('#comment-count-' + postId).text(response.comments_count);
                    
                    // Add new comment to bottom of the list
                    let newCommentHtml = `
                        <div class="d-flex mb-2">
                            <div class="mr-2 mt-1">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <div class="bg-light p-2" style="border-radius: 18px; display: inline-block;">
                                    <strong class="d-block text-sm" style="line-height: 1.2;">${response.comment.user_name}</strong>
                                    <span class="text-sm" style="line-height: 1.2;">${response.comment.content}</span>
                                </div>
                                <div class="text-muted ml-2 mt-1" style="font-size: 11px;">
                                    Sasa hivi &middot; <a href="javascript:void(0);" onclick="$('#replyForm-${response.comment.id}').toggle(); $('#replyInput-${response.comment.id}').focus();" class="text-muted font-weight-bold" style="text-decoration: none;">Reply</a>
                                </div>
                            </div>
                        </div>
                        <div class="ml-5" id="replies-list-${response.comment.id}">
                            <!-- Reply Form -->
                            <form action="${url}" method="POST" class="form-reply mt-2 mb-3" data-post-id="${postId}" data-parent-id="${response.comment.id}" id="replyForm-${response.comment.id}" style="display: none;">
                                <input type="hidden" name="_token" value="${$('input[name="_token"]').val()}">
                                <input type="hidden" name="parent_id" value="${response.comment.id}">
                                <div class="d-flex align-items-center">
                                    <div class="mr-2">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px;">
                                            <i class="fas fa-user" style="font-size: 10px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 position-relative">
                                        <input type="text" name="content" id="replyInput-${response.comment.id}" class="form-control form-control-sm" placeholder="Jibu hapa..." style="border-radius: 20px; padding-right: 35px;" required>
                                        <button type="submit" class="btn btn-sm text-primary position-absolute" style="right: 2px; top: 0px; background: transparent; border: none;">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    `;
                    $('#comments-list-' + postId).append(newCommentHtml);
                    
                    // Re-bind reply form submit for newly added reply form
                    bindReplyForms();

                    // Clear input
                    input.val('');
                }
            }
        });
    });

    // AJAX for Reply
    function bindReplyForms() {
        $('.form-reply').off('submit').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let postId = form.data('post-id');
            let parentId = form.data('parent-id');
            let url = form.attr('action');
            let input = $('#replyInput-' + parentId);
            
            $.ajax({
                type: 'POST',
                url: url,
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        // Update overall comment count
                        $('#comment-count-' + postId).text(response.comments_count);
                        
                        // Add new reply just before the form
                        let newReplyHtml = `
                            <div class="d-flex mb-2">
                                <div class="mr-2 mt-1">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px;">
                                        <i class="fas fa-user" style="font-size: 10px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="bg-light p-2" style="border-radius: 18px; display: inline-block;">
                                        <strong class="d-block text-sm" style="line-height: 1.2;">${response.comment.user_name}</strong>
                                        <span class="text-sm" style="line-height: 1.2;">${response.comment.content}</span>
                                    </div>
                                    <div class="text-muted ml-2 mt-1" style="font-size: 11px;">
                                        Sasa hivi
                                    </div>
                                </div>
                            </div>
                        `;
                        $(newReplyHtml).insertBefore(form);
                        
                        // Clear input and hide form
                        input.val('');
                        form.hide();
                    }
                }
            });
        });
    }

    // Initial binding
    bindReplyForms();
});
</script>
@stop

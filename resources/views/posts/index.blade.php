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
                    <p class="card-text" style="white-space: pre-wrap;">{{ $post->content }}</p>
                    
                    @if($post->image_path)
                        <img src="{{ asset('storage/' . $post->image_path) }}" class="img-fluid rounded mb-3" alt="Post Image">
                    @endif
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted text-sm"><i class="fas fa-thumbs-up text-primary"></i> {{ $post->likes->count() }} Likes</span>
                        <span class="text-muted text-sm">{{ $post->comments->count() }} Comments</span>
                    </div>

                    <div class="d-flex mb-3">
                        <form action="{{ route('posts.like', $post) }}" method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $post->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-thumbs-up"></i> {{ $post->isLikedBy(auth()->user()) ? 'Umelike' : 'Like' }}
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('#commentForm-{{ $post->id }}').toggle(); $('#commentInput-{{ $post->id }}').focus();">
                            <i class="fas fa-comment"></i> Comment
                        </button>
                    </div>

                    <!-- Comments List -->
                    @if($post->comments->count() > 0)
                        <div class="mt-2 pl-3 border-left mb-3">
                            @foreach($post->comments as $comment)
                                <div class="mb-2">
                                    <strong>
                                        @if($comment->user->member)
                                            {{ $comment->user->member->first_name }} {{ $comment->user->member->last_name }}
                                        @else
                                            {{ $comment->user->name }}
                                        @endif
                                    </strong>: 
                                    <span>{{ $comment->content }}</span>
                                    <br>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Comment Form -->
                    <form action="{{ route('posts.comment', $post) }}" method="POST" id="commentForm-{{ $post->id }}" style="display: none;">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" id="commentInput-{{ $post->id }}" class="form-control form-control-sm" placeholder="Andika comment..." required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
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

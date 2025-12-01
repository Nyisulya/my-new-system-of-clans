@extends('adminlte::page')

@section('title', 'News & Updates')

@section('content_header')
    <h1><i class="fas fa-newspaper"></i> News & Updates</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9">
            @forelse($announcements as $announcement)
                <div class="card card-widget">
                    <div class="card-header">
                        <div class="user-block">
                            <span class="username"><a href="#">{{ $announcement->title }}</a></span>
                            <span class="description">Posted by {{ $announcement->creator->name ?? 'Admin' }} - {{ $announcement->start_date->format('F j, Y') }}</span>
                        </div>
                        <div class="card-tools">
                            <span class="badge badge-{{ $announcement->type }}">{{ ucfirst($announcement->type) }}</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-dark" style="white-space: pre-line;">{{ $announcement->content }}</p>
                    </div>
                    <div class="card-footer">
                        <span class="text-muted text-sm">
                            <i class="far fa-clock"></i> {{ $announcement->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> No Announcements</h5>
                    There are no active announcements at this time.
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
@stop

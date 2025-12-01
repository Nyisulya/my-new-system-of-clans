@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell"></i> Notifications</h1>
        <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-check-double"></i> Mark All as Read
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @forelse($notifications as $notification)
                <div class="callout callout-{{ $notification->read_at ? 'default' : 'info' }}">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>
                                @if(!$notification->read_at)
                                    <span class="badge badge-primary">NEW</span>
                                @endif
                                {{ ucfirst(str_replace('_', ' ', $notification->data['type'] ?? 'Notification')) }}
                            </h5>
                            <p>
                                @if($notification->data['type'] == 'birthday')
                                    <strong>{{ $notification->data['member_name'] }}</strong> has a birthday on {{ $notification->data['date'] }}!
                                @elseif($notification->data['type'] == 'anniversary')
                                    <strong>{{ $notification->data['couple'] }}</strong> have their anniversary on {{ $notification->data['date'] }}!
                                @elseif($notification->data['type'] == 'death_anniversary')
                                    Remembering <strong>{{ $notification->data['member_name'] }}</strong> - {{ $notification->data['date'] }}
                                @endif
                            </p>
                            <small class="text-muted">
                                <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div>
                            @if(!$notification->read_at)
                                <a href="{{ route('notifications.mark-read', $notification->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Mark Read
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No notifications yet.
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@stop

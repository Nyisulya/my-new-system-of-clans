@extends('layouts.app')

@section('title', 'Arifa')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell"></i> Arifa</h1>
        <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-check-double"></i> Weka Zote Zimesomwa
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @forelse($notifications as $notification)
                @php
                    $type = $notification->data['type'] ?? 'notification';
                    $typeLabel = match($type) {
                        'birthday' => 'Siku ya Kuzaliwa 🎂',
                        'anniversary' => 'Kumbukumbu ya Ndoa 💍',
                        'death_anniversary' => 'Kumbukumbu ya Kifo 🕯️',
                        'announcement' => 'Tangazo Jipya 📢',
                        default => 'Arifa 🔔'
                    };
                    $calloutClass = $notification->read_at ? 'default' : 'info';
                @endphp
                <div class="callout callout-{{ $calloutClass }} shadow-sm border-0 mb-3" style="border-radius: 8px; border-left: 4px solid {{ $notification->read_at ? '#ced4da' : '#17a2b8' }} !important; background: #fff; padding: 1.25rem;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h5 class="font-weight-bold text-dark mb-2">
                                @if(!$notification->read_at)
                                    <span class="badge badge-success px-2 py-1 mr-2" style="font-size: 0.75rem; border-radius: 4px;">MPYA</span>
                                @endif
                                {{ $typeLabel }}
                            </h5>
                            <div class="text-secondary mb-2" style="font-size: 0.95rem;">
                                @if($type == 'birthday')
                                    Mpendwa <strong>{{ $notification->data['member_name'] ?? 'Mwanachama' }}</strong> anaadhimisha siku yake ya kuzaliwa tarehe <strong>{{ $notification->data['date'] ?? '' }}</strong>! Mtakie heri ya siku ya kuzaliwa.
                                @elseif($type == 'anniversary')
                                    Wapendwa wetu <strong>{{ $notification->data['couple'] ?? 'Wanandoa' }}</strong> wanaadhimisha kumbukumbu ya miaka ya ndoa yao tarehe <strong>{{ $notification->data['date'] ?? '' }}</strong>!
                                @elseif($type == 'death_anniversary')
                                    Kumbukumbu ya Kifo cha mpendwa wetu <strong>{{ $notification->data['member_name'] ?? '' }}</strong> - Tarehe <strong>{{ $notification->data['date'] ?? '' }}</strong>. Endelea kumkumbuka katika sala.
                                @elseif($type == 'announcement')
                                    <div class="font-weight-bold text-dark mb-1">{{ $notification->data['title'] ?? 'Tangazo Jipya' }}</div>
                                    <a href="{{ route('announcements.feed') }}" class="btn btn-sm btn-info font-weight-bold text-white mt-2 px-3 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
                                        <i class="fas fa-eye mr-1"></i> Soma Zaidi
                                    </a>
                                @else
                                    Una arifa mpya kwenye mfumo.
                                @endif
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="ml-3">
                            @if(!$notification->read_at)
                                <div class="action-buttons">
                                    <a href="{{ route('notifications.mark-read', $notification->id) }}" class="btn btn-sm btn-success shadow-sm" style="border-radius: 6px;" title="Weka kuwa Imesomwa">
                                        <i class="fas fa-check"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Hakuna arifa bado.
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@stop

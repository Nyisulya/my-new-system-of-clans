@extends('layouts.app')

@section('title', 'Mstari wa Wakati wa Familia')

@section('content_header')
    <h1><i class="fas fa-history"></i> Mstari wa Wakati wa Familia</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if($groupedEvents->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Hakuna matukio yaliyopatikana. Ongeza wanachama wenye tarehe za kuzaliwa/kufariki au ndoa ili kuijaza mstari wa wakati.
                    </div>
                @else
                    <div class="timeline">
                        @foreach($groupedEvents as $year => $events)
                            <div class="time-label">
                                <span class="bg-red">{{ $year }}</span>
                            </div>

                            @foreach($events as $event)
                                <div>
                                    <i class="{{ $event['icon'] }} {{ $event['color'] }}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $event['date']->translatedFormat('d M') }}</span>
                                        <h3 class="timeline-header">
                                            @if($event['type'] == 'birth')
                                                <a href="{{ route('members.dashboard', $event['model']->id) }}">{{ $event['title'] }}</a>
                                            @elseif($event['type'] == 'death')
                                                <a href="{{ route('members.dashboard', $event['model']->id) }}">{{ $event['title'] }}</a>
                                            @else
                                                {{ $event['title'] }}
                                            @endif
                                        </h3>

                                        <div class="timeline-body">
                                            {{ $event['description'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                        
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>

                    @if($paginatedEvents->hasPages())
                        <div class="d-flex justify-content-center mt-4 mb-4">
                            {{ $paginatedEvents->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@stop

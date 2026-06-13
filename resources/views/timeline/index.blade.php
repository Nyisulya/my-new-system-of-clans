@extends('adminlte::page')

@section('title', 'Family Timeline')

@section('content_header')
    <h1><i class="fas fa-history"></i> Family Timeline</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if($groupedEvents->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No events found. Add members with birth/death dates or marriages to populate the timeline.
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
                                        <span class="time"><i class="fas fa-clock"></i> {{ $event['date']->format('M d') }}</span>
                                        <h3 class="timeline-header">
                                            @if($event['type'] == 'birth')
                                                <a href="{{ route('members.show', $event['model']->id) }}">{{ $event['title'] }}</a>
                                            @elseif($event['type'] == 'death')
                                                <a href="{{ route('members.show', $event['model']->id) }}">{{ $event['title'] }}</a>
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
                @endif
            </div>
        </div>
    </div>
@stop

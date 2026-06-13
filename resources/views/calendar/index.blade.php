@extends('adminlte::page')

@section('title', 'Family Calendar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt text-primary"></i> Family Events Calendar</h1>
        <a href="{{ route('calendar.export') }}" class="btn btn-primary">
            <i class="fas fa-file-export"></i> Export to Calendar (.ics)
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- Left Column: Upcoming Events & Filters --}}
        <div class="col-md-3">
            {{-- Filters --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Filter Events</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterBirthday" value="event-birthday" checked>
                            <label for="filterBirthday" class="custom-control-label text-success">
                                <i class="fas fa-birthday-cake mr-1"></i> Birthdays
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterMarriage" value="event-marriage" checked>
                            <label for="filterMarriage" class="custom-control-label text-warning">
                                <i class="fas fa-ring mr-1"></i> Anniversaries
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterDeath" value="event-death" checked>
                            <label for="filterDeath" class="custom-control-label text-secondary">
                                <i class="fas fa-dove mr-1"></i> Deaths
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Upcoming (30 Days)</h3>
                </div>
                <div class="card-body p-0">
                    @if(empty($upcomingEvents))
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p>No upcoming events.</p>
                        </div>
                    @else
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            @foreach($upcomingEvents as $event)
                                <li class="item">
                                    <div class="product-img">
                                        @if($event['type'] == 'birthday')
                                            <i class="fas fa-birthday-cake fa-2x text-success"></i>
                                        @elseif($event['type'] == 'marriage')
                                            <i class="fas fa-ring fa-2x text-warning"></i>
                                        @else
                                            <i class="fas fa-dove fa-2x text-secondary"></i>
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        @if($event['type'] == 'marriage')
                                            <span class="product-title">
                                                {{ $event['husband']->first_name }} & {{ $event['wife']->first_name }}
                                                <span class="badge float-right badge-warning">
                                                    {{ $event['date']->format('M d') }}
                                                </span>
                                            </span>
                                            <span class="product-description">
                                                {{ $event['years'] }}th Anniversary
                                                @if($event['days_left'] == 0)
                                                    <span class="text-danger font-weight-bold ml-1">(Today!)</span>
                                                @elseif($event['days_left'] == 1)
                                                    <span class="text-warning font-weight-bold ml-1">(Tomorrow)</span>
                                                @endif
                                            </span>
                                        @else
                                            <a href="{{ route('members.dashboard', $event['member']->id) }}" class="product-title">
                                                {{ $event['member']->first_name }}
                                                <span class="badge float-right {{ $event['type'] == 'birthday' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $event['date']->format('M d') }}
                                                </span>
                                            </a>
                                            <span class="product-description">
                                                @if($event['type'] == 'birthday')
                                                    Turning {{ $event['age'] }}
                                                @else
                                                    {{ $event['years'] }} Years Gone
                                                @endif
                                                @if($event['days_left'] == 0)
                                                    <span class="text-danger font-weight-bold ml-1">(Today!)</span>
                                                @elseif($event['days_left'] == 1)
                                                    <span class="text-warning font-weight-bold ml-1">(Tomorrow)</span>
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Full Calendar --}}
        <div class="col-md-9">
            <div class="card card-primary">
                <div class="card-body p-0">
                    {{-- THE CALENDAR --}}
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <style>
        .fc-event { cursor: pointer; font-size: 0.9em; }
        .fc-day-grid-event .fc-content { white-space: normal; }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        $(function () {
            var allEvents = @json($events);

            var calendar = $('#calendar').fullCalendar({
                header    : {
                    left  : 'prev,next today',
                    center: 'title',
                    right : 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    month: 'month',
                    week : 'week',
                    day  : 'day'
                },
                events    : allEvents,
                editable  : false,
                droppable : false,
                height: 'auto',
                eventClick: function(event) {
                    if (event.url) {
                        window.open(event.url, "_self");
                        return false;
                    }
                },
                eventRender: function(event, element) {
                    // Filter logic
                    var showBirthday = $('#filterBirthday').is(':checked');
                    var showMarriage = $('#filterMarriage').is(':checked');
                    var showDeath = $('#filterDeath').is(':checked');

                    if (event.className.includes('event-birthday') && !showBirthday) {
                        return false;
                    }
                    if (event.className.includes('event-marriage') && !showMarriage) {
                        return false;
                    }
                    if (event.className.includes('event-death') && !showDeath) {
                        return false;
                    }
                }
            });

            // Re-render events when filters change
            $('.event-filter').change(function() {
                $('#calendar').fullCalendar('rerenderEvents');
            });
        })
    </script>
@stop

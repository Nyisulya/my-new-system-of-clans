@extends('layouts.app')

@section('title', 'Kalenda ya Familia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt text-primary"></i> Kalenda ya Matukio ya Familia</h1>
        <a href="{{ route('calendar.export') }}" class="btn btn-primary">
            <i class="fas fa-file-export"></i> Hamisha Kalenda (.ics)
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- Safu ya Kushoto: Matukio Yanayokuja & Vichujio --}}
        <div class="col-md-3">
            {{-- Vichujio --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Chuja Matukio</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterBirthday" value="event-birthday" checked>
                            <label for="filterBirthday" class="custom-control-label text-success">
                                <i class="fas fa-birthday-cake mr-1"></i> Siku za Kuzaliwa
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterMarriage" value="event-marriage" checked>
                            <label for="filterMarriage" class="custom-control-label text-warning">
                                <i class="fas fa-ring mr-1"></i> Miaka ya Ndoa
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input event-filter" type="checkbox" id="filterDeath" value="event-death" checked>
                            <label for="filterDeath" class="custom-control-label text-secondary">
                                <i class="fas fa-dove mr-1"></i> Vifo
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Matukio Yanayokuja --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Yanayokuja (Siku 30)</h3>
                </div>
                <div class="card-body p-0">
                    @if(empty($upcomingEvents))
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p>Hakuna matukio yanayokuja.</p>
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
                                                    {{ $event['date']->format('d M') }}
                                                </span>
                                            </span>
                                            <span class="product-description">
                                                Miaka {{ $event['years'] }} ya Ndoa
                                                @if($event['days_left'] == 0)
                                                    <span class="text-danger font-weight-bold ml-1">(Leo!)</span>
                                                @elseif($event['days_left'] == 1)
                                                    <span class="text-warning font-weight-bold ml-1">(Kesho)</span>
                                                @endif
                                            </span>
                                        @else
                                            <a href="{{ route('members.dashboard', $event['member']->id) }}" class="product-title">
                                                {{ $event['member']->first_name }}
                                                <span class="badge float-right {{ $event['type'] == 'birthday' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $event['date']->format('d M') }}
                                                </span>
                                            </a>
                                            <span class="product-description">
                                                @if($event['type'] == 'birthday')
                                                    Anafikia miaka {{ $event['age'] }}
                                                @else
                                                    Miaka {{ $event['years'] }} Tangu Afariki
                                                @endif
                                                @if($event['days_left'] == 0)
                                                    <span class="text-danger font-weight-bold ml-1">(Leo!)</span>
                                                @elseif($event['days_left'] == 1)
                                                    <span class="text-warning font-weight-bold ml-1">(Kesho)</span>
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

        {{-- Safu ya Kulia: Kalenda Kamili --}}
        <div class="col-md-9">
            <div class="card card-primary">
                <div class="card-body p-0">
                    {{-- KALENDA --}}
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
                    today: 'Leo',
                    month: 'Mwezi',
                    week : 'Wiki',
                    day  : 'Siku'
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
                    // Mantiki ya kuchuja
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

            // Onyesha tena matukio vichujio vikibadilika
            $('.event-filter').change(function() {
                $('#calendar').fullCalendar('rerenderEvents');
            });
        })
    </script>
@stop

@extends('adminlte::page')

@section('title', $family->name . ' - Family Tree')

@section('content_header')
    <h1>
        <i class="fas fa-sitemap"></i> {{ $family->name }} Family Tree
        <small>Members Overview</small>
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-table"></i> Family Members</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <button id="toggle-founder-view" class="btn btn-warning btn-lg btn-block">
                        <i class="fas fa-crown"></i> <span id="toggle-text">Show Clan Founder Only</span>
                    </button>
                    <small class="text-muted d-block text-center mt-2">
                        <i class="fas fa-info-circle"></i> Toggle to view only Generation 1 (Founder + Wives) or all generations
                    </small>
                </div>
                <div class="col-md-3">
                    <label>Generation</label>
                    <select id="filter-generation" class="form-control select2">
                        <option value="">All Generations</option>
                        @foreach($allMembers->pluck('generation_number')->unique()->sort() as $gen)
                            <option value="{{ $gen }}">Generation {{ $gen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Gender</label>
                    <select id="filter-gender" class="form-control select2">
                        <option value="">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select id="filter-status" class="form-control select2">
                        <option value="">All Statuses</option>
                        <option value="Alive">Alive</option>
                        <option value="Deceased">Deceased</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="membersTable" class="table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                        <tr>
                            <th></th> {{-- Expand button --}}
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Gen</th>
                            <th>Gender</th>
                            <th>Children</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allMembers as $member)
                            @php
                                // Check if this member is Gen 1 or spouse of Gen 1
                                $isGenOne = $member->generation_number == 1;
                                $isSpouseOfGenOne = false;
                                
                                if (!$isGenOne) {
                                    // Check if they're married to a Gen 1 member
                                    $spouses = $member->spouses();
                                    foreach ($spouses as $spouse) {
                                        if ($spouse->generation_number == 1) {
                                            $isSpouseOfGenOne = true;
                                            break;
                                        }
                                    }
                                }
                                
                                $founderGroup = $isGenOne || $isSpouseOfGenOne ? 'yes' : 'no';
                            @endphp
                            <tr data-child-value="{{ $member->biography ?? 'No biography available.' }}" 
                                data-founder-group="{{ $founderGroup }}">
                                <td class="details-control text-center text-primary" style="cursor: pointer;">
                                    <i class="fas fa-plus-circle"></i>
                                </td>
                                <td>
                                    <img src="{{ $member->profile_photo_url }}" alt="Photo" class="img-circle img-size-32 mr-2 border">
                                </td>
                                <td>
                                    <strong>{{ $member->full_name }}</strong>
                                    @if($isSpouseOfGenOne && !$isGenOne)
                                        <span class="badge badge-info badge-sm ml-1">Spouse</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">
                                        {{ $member->date_of_birth ? $member->date_of_birth->format('Y') : '?' }} - 
                                        {{ $member->status == 'deceased' && $member->date_of_death ? $member->date_of_death->format('Y') : ($member->status == 'alive' ? 'Present' : '?') }}
                                    </small>
                                </td>
                                <td><span class="badge badge-light">{{ $member->generation_number }}</span></td>
                                <td>
                                    @if($member->gender == 'male')
                                        <i class="fas fa-mars text-blue"></i> Male
                                    @elseif($member->gender == 'female')
                                        <i class="fas fa-venus text-pink"></i> Female
                                    @else
                                        {{ ucfirst($member->gender) }}
                                    @endif
                                </td>
                                <td>
                                    @if($member->children->count() > 0)
                                        <span class="badge badge-info">{{ $member->children->count() }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member->status == 'alive')
                                        <span class="badge badge-success">Alive</span>
                                    @else
                                        <span class="badge badge-secondary">Deceased</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('members.dashboard', $member->id) }}" class="btn btn-xs btn-primary" title="Dashboard">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </a>
                                    @php
                                        $spouses = $member->spouses();
                                        $spouseCount = is_countable($spouses) ? $spouses->count() : 0;
                                        $firstSpouse = null;
                                        if ($spouseCount > 0) {
                                            $firstSpouse = $spouses->first();
                                        }
                                    @endphp
                                    <a href="{{ route('members.create', [
                                        'father_id' => $member->gender == 'male' ? $member->id : ($firstSpouse?->id ?? null),
                                        'mother_id' => $member->gender == 'female' ? $member->id : ($firstSpouse?->id ?? null),
                                        'clan_id' => $member->clan_id,
                                        'family_id' => $member->family_id
                                    ]) }}" class="btn btn-xs btn-success" title="Add Child">
                                        <i class="fas fa-baby"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)

@section('js')
    {{-- DataTables Buttons CDNs --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            var showingFounderOnly = false;

            // DataTable Init
            var table = $('#membersTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[ 3, "asc" ]], // Order by Generation
                "dom": 'Bfrtip',
                "buttons": [
                    { extend: 'copy', className: 'btn btn-default btn-sm' },
                    { extend: 'csv', className: 'btn btn-default btn-sm' },
                    { extend: 'excel', className: 'btn btn-default btn-sm' },
                    { extend: 'pdf', className: 'btn btn-default btn-sm' },
                    { extend: 'print', className: 'btn btn-default btn-sm' },
                    { extend: 'colvis', className: 'btn btn-default btn-sm' }
                ]
            });

            // Toggle Founder View Button
            $('#toggle-founder-view').on('click', function() {
                showingFounderOnly = !showingFounderOnly;
                
                if (showingFounderOnly) {
                    // Show only Generation 1 members AND their spouses
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var row = table.row(dataIndex).node();
                            return $(row).attr('data-founder-group') === 'yes';
                        }
                    );
                    table.draw();
                    
                    $(this).removeClass('btn-warning').addClass('btn-success');
                    $('#toggle-text').text('Show All Generations');
                    $(this).find('i').removeClass('fa-crown').addClass('fa-sitemap');
                    
                    // Disable generation filter dropdown
                    $('#filter-generation').val('').prop('disabled', true);
                } else {
                    // Show all generations - remove the custom filter
                    $.fn.dataTable.ext.search.pop();
                    table.draw();
                    
                    $(this).removeClass('btn-success').addClass('btn-warning');
                    $('#toggle-text').text('Show Clan Founder Only');
                    $(this).find('i').removeClass('fa-sitemap').addClass('fa-crown');
                    
                    // Enable generation filter dropdown
                    $('#filter-generation').prop('disabled', false);
                }
            });

            // Custom Filters
            $('#filter-generation').on('change', function () {
                if (!showingFounderOnly) {
                    table.column(3).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
                }
            });
            $('#filter-gender').on('change', function () {
                table.column(4).search(this.value ? this.value : '', true, false).draw();
            });
            $('#filter-status').on('change', function () {
                table.column(6).search(this.value ? this.value : '', true, false).draw();
            });

            // Expandable Rows
            $('#membersTable tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
                } else {
                    row.child(format(tr.data('child-value'))).show();
                    tr.addClass('shown');
                    icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
                }
            });

            function format(d) {
                return '<div class="p-3 bg-light border-left border-info">' +
                    '<strong>Biography/Notes:</strong><br>' +
                    d +
                    '</div>';
            }
        });
    </script>
@stop

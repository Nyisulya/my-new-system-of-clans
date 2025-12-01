@extends('adminlte::page')

@section('title', 'Smart Relationship Calculator')

@section('content_header')
    <h1><i class="fas fa-calculator"></i> Smart Relationship Calculator</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Find out how two people are related</h3>
                </div>
                <div class="card-body">
                    <form id="relationship-form">
                        <div class="form-group">
                            <label>First Person</label>
                            <select class="form-control select2" id="member1_id" name="member1_id" style="width: 100%;">
                                <option value="">Select Person...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center my-3">
                            <i class="fas fa-exchange-alt fa-2x text-muted"></i>
                        </div>

                        <div class="form-group">
                            <label>Second Person</label>
                            <select class="form-control select2" id="member2_id" name="member2_id" style="width: 100%;">
                                <option value="">Select Person...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                            <i class="fas fa-search"></i> Calculate Relationship
                        </button>
                    </form>
                </div>
            </div>

            <div class="card card-success" id="result-card" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">Result</h3>
                </div>
                <div class="card-body text-center">
                    <h4><span id="p1-name"></span> is <span id="p2-name"></span>'s:</h4>
                    <h2 class="text-success font-weight-bold my-4" id="relationship-result"></h2>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap'
            });

            $('#relationship-form').on('submit', function(e) {
                e.preventDefault();
                
                const m1 = $('#member1_id').val();
                const m2 = $('#member2_id').val();

                if (!m1 || !m2) {
                    alert('Please select both members');
                    return;
                }

                if (m1 === m2) {
                    alert('Please select different members');
                    return;
                }

                $.ajax({
                    url: '{{ route("relationships.calculate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        member1_id: m1,
                        member2_id: m2
                    },
                    success: function(response) {
                        $('#p1-name').text(response.member1);
                        $('#p2-name').text(response.member2);
                        $('#relationship-result').text(response.result);
                        $('#result-card').slideDown();
                    },
                    error: function(xhr) {
                        alert('Error calculating relationship');
                    }
                });
            });
        });
    </script>
@stop

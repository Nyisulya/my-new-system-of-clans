@extends('layouts.app')

@section('title', 'Kikokotoo cha Uhusiano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calculator text-primary"></i> Kikokotoo cha Uhusiano (Smart Calculator)</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashibodi</a></li>
            <li class="breadcrumb-item active">Kikokotoo cha Uhusiano</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
            <div class="card card-primary card-outline shadow-sm" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #007bff;">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-search-location mr-1 text-primary"></i> 
                        Tafuta Uhusiano wa Wanafamilia
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form id="relationship-form">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted" style="font-size: 0.9rem;">Mtu wa Kwanza</label>
                            <select class="form-control select2" id="member1_id" name="member1_id" style="width: 100%;">
                                <option value="">Chagua Mwanachama...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center my-4">
                            <div class="exchange-icon-bg" style="display: inline-block; background: #e9ecef; border-radius: 50%; width: 45px; height: 45px; line-height: 45px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                <i class="fas fa-exchange-alt text-secondary" style="transform: rotate(90deg); font-size: 1.1rem;"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-muted" style="font-size: 0.9rem;">Mtu wa Pili</label>
                            <select class="form-control select2" id="member2_id" name="member2_id" style="width: 100%;">
                                <option value="">Chagua Mwanachama...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4 font-weight-bold shadow-sm" style="border-radius: 8px; transition: all 0.2s;">
                            <i class="fas fa-sync-alt mr-1"></i> Kokotoa Uhusiano
                        </button>
                    </form>
                </div>
            </div>

            <div class="card card-success shadow-sm mt-4" id="result-card" style="display: none; border-radius: 12px; border-top: 3px solid #28a745; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h3 class="card-title text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1 text-success"></i> 
                        Matokeo ya Uhusiano
                    </h3>
                </div>
                <div class="card-body text-center pb-5 px-4">
                    <div class="relationship-display p-4 bg-light rounded" style="border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                        <h4 class="text-dark mb-0 font-weight-normal" style="line-height: 1.8;">
                            <span id="p1-name" class="font-weight-bold text-primary"></span>
                            ni 
                            <span id="relationship-result" class="font-weight-bold text-success" style="font-size: 1.4rem; padding: 2px 8px; border-bottom: 2px solid #28a745;"></span> 
                            wa 
                            <span id="p2-name" class="font-weight-bold text-primary"></span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #495057 !important;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,123,255,0.2) !important;
        }
    </style>
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
                    alert('Tafadhali chagua wanafamilia wote wawili.');
                    return;
                }

                if (m1 === m2) {
                    alert('Tafadhali chagua wanafamilia wawili tofauti.');
                    return;
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const origHtml = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Inakokotoa...').prop('disabled', true);
                $('#result-card').slideUp();

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
                        alert('Kuna hitilafu iliyotokea wakati wa kukokotoa uhusiano.');
                    },
                    complete: function() {
                        submitBtn.html(origHtml).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@stop

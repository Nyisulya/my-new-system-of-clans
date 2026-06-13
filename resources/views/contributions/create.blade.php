@extends('adminlte::page')

@section('title', 'Record Contribution')

@section('content_header')
    <h1>Record Contribution</h1>
@stop

@section('content')
    <div class="card card-success">
        <form id="contributionForm" action="{{ route('contributions.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Campaign</label>
                    <select name="campaign_id" id="campaign_id" class="form-control select2" required>
                        <option value="">Select Campaign</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}" {{ (isset($campaign) && $campaign->id == $c->id) ? 'selected' : '' }}>
                                {{ $c->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Member (Contributor)</label>
                    <select name="member_id" id="member_id" class="form-control select2" required>
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">
                                {{ $member->full_name }} ({{ $member->family->name ?? 'No Family' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tsh</span>
                                </div>
                                <input type="number" name="amount" id="amount" class="form-control" required min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="method" id="method" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="M-Pesa">M-Pesa</option>
                                <option value="Tigo Pesa">Tigo Pesa</option>
                                <option value="Airtel Money">Airtel Money</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Notes (Optional)</label>
                            <input type="text" name="notes" class="form-control" placeholder="Reference number, etc.">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success" id="btnSave">Save Contribution</button>
                <button type="button" class="btn btn-primary ml-2" id="btnPayMobile">
                    <i class="fas fa-mobile-alt"></i> Pay with Mobile Money
                </button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap'
            });

            $('#btnPayMobile').click(function() {
                var campaignId = $('#campaign_id').val();
                var memberId = $('#member_id').val();
                var amount = $('#amount').val();

                if (!campaignId || !memberId || !amount) {
                    Swal.fire('Error', 'Please select Campaign, Member and enter Amount first.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Enter Phone Number',
                    input: 'text',
                    inputLabel: 'Please enter the M-Pesa/Tigo Pesa number',
                    inputPlaceholder: '07...',
                    showCancelButton: true,
                    confirmButtonText: 'Pay Now',
                    showLoaderOnConfirm: true,
                    preConfirm: (phone) => {
                        return $.ajax({
                            url: "{{ route('contributions.pay') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                campaign_id: campaignId,
                                member_id: memberId,
                                amount: amount,
                                phone: phone
                            },
                            success: function(response) {
                                return response;
                            },
                            error: function(xhr) {
                                Swal.showValidationMessage(
                                    `Request failed: ${xhr.responseJSON.message}`
                                );
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Payment Successful!',
                            text: 'Contribution has been recorded.',
                            icon: 'success'
                        }).then(() => {
                            window.location.href = result.value.redirect_url;
                        });
                    }
                });
            });
        });
    </script>
@stop

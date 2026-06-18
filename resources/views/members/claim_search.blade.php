@extends('layouts.app')

@section('title', __('common.search_profile_title'))
@section('page_title', __('common.search_profile_title'))

@section('content_header')
    <h1>
        <i class="fas fa-search-plus"></i> {{ __('common.search_profile_title') }}
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Search Card --}}
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-search mr-2 text-primary"></i>
                        {{ __('common.search_profile_title') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        {{ __('common.unlinked_profile_warning') }}
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <div class="input-group input-group-lg shadow-sm rounded">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" id="search-input" class="form-control border-left-0 py-3" 
                                   placeholder="{{ __('common.search_profile_placeholder') }}" autofocus autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" type="button" id="search-button">
                                    {{ __('common.search') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Loading Spinner --}}
                    <div id="loading" class="text-center my-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Inapakia...</span>
                        </div>
                        <p class="text-muted mt-2">{{ __('common.loading_members') }}</p>
                    </div>

                    {{-- Search Results --}}
                    <div id="search-results" class="mt-4">
                        {{-- Results will be rendered here dynamically --}}
                    </div>

                    {{-- No Results Alert (Default hidden) --}}
                    <div id="no-results" class="alert alert-info d-none text-center p-4">
                        <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                        <h5>{{ __('common.no_profile_found') }}</h5>
                        <p class="mb-3 text-muted">Kama huoni jina lako, inawezekana wasifu wako haujasajiliwa bado na Admin.</p>
                        <a href="{{ route('members.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus mr-1"></i> {{ __('common.register_new_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="claimConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="claimModalLabel">
                    <i class="fas fa-user-check mr-2"></i> {{ __('common.confirm_claim_title') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('profile.claim.submit') }}" method="POST" id="claim-form">
                @csrf
                <input type="hidden" name="member_id" id="modal-member-id">
                
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-light d-inline-block rounded-circle p-3 mb-2">
                            <i class="fas fa-question-circle fa-3x text-primary"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mt-2" id="modal-member-name-title"></h4>
                    </div>

                    <div class="alert alert-warning border-0 shadow-sm p-3 mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mr-3 mt-1"></i>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">{{ __('common.status') }}</h6>
                                <p class="text-muted small mb-0">{{ __('common.confirm_claim_warning') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top-0 d-flex justify-content-between p-3">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-check mr-1"></i> {{ __('common.yes_its_me') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .claim-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 8px;
    }
    .claim-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
    }
    .claim-avatar {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #dee2e6;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let debounceTimer;
    const searchInput = $('#search-input');
    const searchResults = $('#search-results');
    const loading = $('#loading');
    const noResults = $('#no-results');

    // Debounce search on keyup
    searchInput.on('keyup', function() {
        clearTimeout(debounceTimer);
        const query = $(this).val().trim();
        
        if (query.length < 2) {
            searchResults.empty();
            noResults.addClass('d-none');
            return;
        }

        debounceTimer = setTimeout(function() {
            performSearch(query);
        }, 300);
    });

    // Handle click of search button
    $('#search-button').on('click', function() {
        const query = searchInput.val().trim();
        if (query.length >= 2) {
            performSearch(query);
        }
    });

    function performSearch(query) {
        loading.removeClass('d-none');
        searchResults.empty();
        noResults.addClass('d-none');

        $.ajax({
            url: "{{ route('profile.claim.ajax_search') }}",
            type: "GET",
            data: { query: query },
            dataType: "json",
            success: function(data) {
                loading.addClass('d-none');
                
                if (data.length === 0) {
                    noResults.removeClass('d-none');
                    return;
                }

                let html = '<h5 class="mb-3 font-weight-bold text-muted"><i class="fas fa-list-ul mr-1"></i> ' + "{{ __('common.search_results') }}" + '</h5>';
                html += '<div class="row">';

                data.forEach(function(member) {
                    const avatar = member.photo_url ? member.photo_url : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(member.full_name) + '&color=7F9CF5&background=EBF4FF';
                    
                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="card claim-card shadow-sm border h-100">
                                <div class="card-body d-flex align-items-center">
                                    <img src="${avatar}" alt="${member.full_name}" class="claim-avatar mr-3">
                                    <div class="flex-grow-1 min-width-0">
                                        <h6 class="font-weight-bold text-dark mb-1 text-truncate">${member.full_name}</h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-birthday-cake mr-1 text-primary"></i> ${member.date_of_birth}
                                        </p>
                                        <p class="text-muted small mb-0 text-truncate">
                                            <i class="fas fa-users mr-1 text-secondary"></i> ${member.clan_name} &bull; ${member.family_name}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0 pt-0 text-right">
                                    <button class="btn btn-sm btn-outline-primary link-profile-btn" 
                                            data-id="${member.id}" 
                                            data-name="${member.full_name}" 
                                            data-dob="${member.date_of_birth}"
                                            data-has-dob="${member.has_dob ? 1 : 0}">
                                        <i class="fas fa-link mr-1"></i> ` + "{{ __('common.link_to_my_account') }}" + `
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                searchResults.html(html);
            },
            error: function() {
                loading.addClass('d-none');
                alert('Itifaki ya utafutaji imeshindikana. Tafadhali jaribu tena.');
            }
        });
    }

    // Modal Trigger
    $(document).on('click', '.link-profile-btn', function() {
        const memberId = $(this).data('id');
        const memberName = $(this).data('name');
        const memberDob = $(this).data('dob');
        const hasDob = $(this).data('has-dob');

        $('#modal-member-id').val(memberId);
        
        let confirmationMsg = '';
        if (hasDob == 1) {
            confirmationMsg = "{{ __('common.confirm_claim_question', ['name' => ':name', 'dob' => ':dob']) }}"
                                        .replace(':name', memberName)
                                        .replace(':dob', memberDob);
        } else {
            confirmationMsg = "{{ __('common.confirm_claim_question_without_dob', ['name' => ':name']) }}"
                                        .replace(':name', memberName);
        }
        
        $('#modal-member-name-title').text(confirmationMsg);
        $('#claimConfirmationModal').modal('show');
    });
});
</script>
@stop

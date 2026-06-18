@extends('layouts.app')

@section('title', __('common.system_users'))
@section('page_title', __('common.system_users'))

@section('content_header')
    <h1><i class="fas fa-users-cog"></i> {{ __('common.system_users') }}</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> {{ __('common.system_users') }}</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('common.username') }}</th>
                                    <th>{{ __('common.role') }}</th>
                                    <th>{{ __('common.linked_member') }}</th>
                                    <th>{{ __('common.registration_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                    <tr>
                                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>
                                            @if($user->isAdmin())
                                                <span class="badge badge-danger">Admin</span>
                                            @elseif($user->isEditor())
                                                <span class="badge badge-warning">Editor</span>
                                            @elseif($user->isViewer())
                                                <span class="badge badge-info">Viewer</span>
                                            @else
                                                <span class="badge badge-secondary">Member</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->member)
                                                <a href="{{ route('members.dashboard', $user->member->id) }}" class="text-primary font-weight-bold">
                                                    <i class="fas fa-user mr-1"></i> {{ $user->member->full_name }}
                                                </a>
                                            @else
                                                <span class="text-muted italic"><i class="fas fa-user-slash mr-1"></i> {{ __('common.not_linked') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('common.no_users_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

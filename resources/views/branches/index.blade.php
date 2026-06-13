@extends('adminlte::page')

@section('title', 'Branches')

@section('content_header')
    <h1><i class="fas fa-code-branch"></i> Branches</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Branches</h3>
            <div class="card-tools">
                <a href="{{ route('branches.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Add Branch
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Family</th>
                        <th>Location</th>
                        <th>Members</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td><strong>{{ $branch->name }}</strong></td>
                            <td>{{ $branch->family->name ?? 'N/A' }}</td>
                            <td>{{ $branch->location ?? 'N/A' }}</td>
                            <td><span class="badge badge-primary">{{ $branch->member_count }}</span></td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No branches found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
@stop

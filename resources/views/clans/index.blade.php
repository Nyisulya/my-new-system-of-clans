@extends('adminlte::page')

@section('title', 'Clans')

@section('content_header')
    <h1><i class="fas fa-sitemap"></i> Clans</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Clans</h3>
            <div class="card-tools">
                <a href="{{ route('clans.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Add Clan
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Origin</th>
                        <th>Founded</th>
                        <th>Families</th>
                        <th>Members</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clans as $clan)
                        <tr>
                            <td><strong>{{ $clan->name }}</strong></td>
                            <td>{{ $clan->origin_location ?? 'N/A' }}</td>
                            <td>{{ $clan->founding_date?->format('Y') ?? 'N/A' }}</td>
                            <td><span class="badge badge-info">{{ $clan->families_count }}</span></td>
                            <td><span class="badge badge-primary">{{ $clan->members_count }}</span></td>
                            <td>
                                @if($clan->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clans.edit', $clan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clans.destroy', $clan) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this clan? This action cannot be undone.')" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No clans found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $clans->links() }}
            </div>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Families')

@section('content_header')
    <h1><i class="fas fa-home"></i> Families</h1>
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
            <h3 class="card-title">All Families</h3>
            <div class="card-tools">
                <a href="{{ route('families.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Add Family
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Clan</th>
                        <th>Founder</th>
                        <th>Members</th>
                        <th>Established</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($families as $family)
                        <tr>
                            <td><strong>{{ $family->name }}</strong></td>
                            <td>{{ $family->surname }}</td>
                            <td>{{ $family->clan->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $founder = $family->members->first();
                                @endphp
                                @if($founder)
                                    <a href="{{ route('members.dashboard', $founder) }}">{{ $founder->full_name }}</a>
                                @else
                                    <span class="text-muted">No founder</span>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $family->members_count }}</span></td>
                            <td>{{ $family->established_date?->format('Y') ?? 'N/A' }}</td>
                            <td>
                                @if($family->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{-- Crown button - goes to founder page with dashboard layout --}}
                                <a href="{{ route('families.founder', $family) }}" class="btn btn-sm btn-warning" title="View Founder Dashboard">
                                    <i class="fas fa-crown"></i>
                                </a>
                                
                                <a href="{{ route('families.tree', $family) }}" class="btn btn-sm btn-info" title="View Full Tree">
                                    <i class="fas fa-sitemap"></i>
                                </a>
                                <form action="{{ route('families.destroy', $family) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete Family" 
                                            onclick="return confirm('Are you sure you want to delete {{ $family->name }}? This will also delete all members in this family. This action cannot be undone!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No families found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $families->links() }}
            </div>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Announcements')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Announcement
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr>
                            <td>{{ $announcement->title }}</td>
                            <td>
                                <span class="badge badge-{{ $announcement->type }}">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                            </td>
                            <td>{{ $announcement->start_date->format('M d, Y') }}</td>
                            <td>
                                {{ $announcement->end_date ? $announcement->end_date->format('M d, Y') : 'Indefinite' }}
                            </td>
                            <td>
                                @php
                                    $isActive = now()->startOfDay()->between($announcement->start_date, $announcement->end_date ?? now()->addYears(100));
                                    $isFuture = $announcement->start_date->isFuture();
                                @endphp
                                
                                @if($isFuture)
                                    <span class="badge badge-warning">Scheduled</span>
                                @elseif($isActive)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Expired</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

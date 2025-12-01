@extends('adminlte::page')

@section('title', 'First Generation (Founders)')

@section('content_header')
    <h1><i class="fas fa-user-friends"></i> First Generation (Founders)</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Clan Founders</h3>
            <div class="card-tools">
                <a href="{{ route('members.create', ['generation_number' => 1]) }}" class="btn btn-success btn-sm mr-2">
                    <i class="fas fa-plus"></i> Add Founder
                </a>
                <span class="badge badge-info">{{ $parents->count() }} Founders</span>
            </div>
        </div>
        <div class="card-body">
            @if($parents->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No first generation founders found in the system.
                    <p class="mt-2">Click the <strong>"Add Founder"</strong> button above to add the first generation clan founders.</p>
                </div>
            @else
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parents as $parent)
                            <tr>
                                <td>
                                    <strong>{{ $parent->full_name }}</strong>
                                    @if($parent->clan || $parent->family)
                                        <br>
                                        <small class="text-muted">
                                            @if($parent->clan)
                                                {{ $parent->clan->name }}
                                            @endif
                                            @if($parent->clan && $parent->family)
                                                -
                                            @endif
                                            @if($parent->family)
                                                {{ $parent->family->name }}
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('members.dashboard', $parent) }}" class="btn btn-sm btn-primary mr-1">
                                        <i class="fas fa-tachometer-alt"></i> View Dashboard
                                    </a>
                                    <form action="{{ route('members.destroy', $parent) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this founder? This will remove them from the system.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop

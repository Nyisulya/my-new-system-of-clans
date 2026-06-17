@extends('layouts.app')

@section('title', 'Matawi')

@section('content_header')
    <h1><i class="fas fa-code-branch"></i> Matawi</h1>
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
            <h3 class="card-title">Matawi Yote</h3>
            <div class="card-tools">
                <a href="{{ route('branches.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Ongeza Tawi
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jina</th>
                        <th>Familia</th>
                        <th>Mahali</th>
                        <th>Wanachama</th>
                        <th>Hali</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td><strong>{{ $branch->name }}</strong></td>
                            <td>{{ $branch->family->name ?? 'Haijulikani' }}</td>
                            <td>{{ $branch->location ?? 'Haijulikani' }}</td>
                            <td><span class="badge badge-primary">{{ $branch->member_count }}</span></td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge badge-success">Hai</span>
                                @else
                                    <span class="badge badge-secondary">Haifanyi kazi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Hakuna matawi yaliyopatikana</td>
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

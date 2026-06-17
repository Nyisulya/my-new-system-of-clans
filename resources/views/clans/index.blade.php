@extends('layouts.app')

@section('title', 'Ukoo')

@section('content_header')
    <h1><i class="fas fa-sitemap"></i> Ukoo</h1>
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
            <h3 class="card-title">Ukoo Wote</h3>
            <div class="card-tools">
                <a href="{{ route('clans.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Ongeza Ukoo
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jina</th>
                        <th>Asili</th>
                        <th>Kuanzishwa</th>
                        <th>Familia</th>
                        <th>Wanachama</th>
                        <th>Hali</th>
                        <th>Vitendo</th>
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
                                    <span class="badge badge-success">Hai</span>
                                @else
                                    <span class="badge badge-secondary">Haitumiki</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clans.edit', $clan) }}" class="btn btn-sm btn-warning" title="Hariri">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clans.destroy', $clan) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Je, una uhakika unataka kufuta ukoo huu? Kitendo hiki hakiwezi kubatilishwa.')" 
                                            title="Futa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Hakuna ukoo uliopatikana</td>
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

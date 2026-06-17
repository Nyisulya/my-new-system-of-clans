@extends('layouts.app')

@section('title', 'Familia')

@section('content_header')
    <h1><i class="fas fa-home"></i> Familia</h1>
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
            <h3 class="card-title">Familia Zote</h3>
            <div class="card-tools">
                <a href="{{ route('families.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Ongeza Familia
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jina</th>
                        <th>Jina la Ukoo</th>
                        <th>Ukoo</th>
                        <th>Mwanzilishi</th>
                        <th>Wanachama</th>
                        <th>Ilianzishwa</th>
                        <th>Hali</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($families as $family)
                        <tr>
                            <td><strong>{{ $family->name }}</strong></td>
                            <td>{{ $family->surname }}</td>
                            <td>{{ $family->clan->name ?? 'Haijulikani' }}</td>
                            <td>
                                @php
                                    $founder = $family->members->first();
                                @endphp
                                @if($founder)
                                    <a href="{{ route('members.dashboard', $founder) }}">{{ $founder->full_name }}</a>
                                @else
                                    <span class="text-muted">Hakuna mwanzilishi</span>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $family->members_count }}</span></td>
                            <td>{{ $family->established_date?->format('Y') ?? 'Haijulikani' }}</td>
                            <td>
                                @if($family->is_active)
                                    <span class="badge badge-success">Hai</span>
                                @else
                                    <span class="badge badge-secondary">Haifanyi kazi</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('families.founder', $family) }}" class="btn btn-xs btn-warning" title="Dashibodi ya Mwanzilishi">
                                        <i class="fas fa-crown"></i>
                                    </a>
                                    <a href="{{ route('families.tree', $family) }}" class="btn btn-xs btn-info" title="Angalia Mti Kamili">
                                        <i class="fas fa-sitemap"></i>
                                    </a>
                                    <form action="{{ route('families.destroy', $family) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Futa Familia" 
                                                onclick="return confirm('Je, una uhakika unataka kufuta {{ $family->name }}? Hii itafuta pia wanachama wote wa familia hii. Kitendo hiki hakiwezi kutenduliwa!')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Hakuna familia zilizopatikana</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-3">
                {{ $families->links() }}
            </div>
        </div>
    </div>
@stop

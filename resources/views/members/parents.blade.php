@extends('layouts.app')

@section('title', 'Kizazi cha Kwanza (Waanzilishi)')

@section('content_header')
    <h1><i class="fas fa-user-friends"></i> Kizazi cha Kwanza (Waanzilishi)</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Waanzilishi wa Ukoo</h3>
            <div class="card-tools">
                <a href="{{ route('members.create', ['generation_number' => 1]) }}" class="btn btn-success btn-sm mr-2">
                    <i class="fas fa-plus"></i> Ongeza Mwanzilishi
                </a>
                <span class="badge badge-info">{{ $parents->count() }} Waanzilishi</span>
            </div>
        </div>
        <div class="card-body">
            @if($parents->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Hakuna waanzilishi wa kizazi cha kwanza waliopatikana kwenye mfumo.
                    <p class="mt-2">Bofya kitufe cha <strong>"Ongeza Mwanzilishi"</strong> hapo juu ili kuongeza waanzilishi wa kizazi cha kwanza cha ukoo.</p>
                </div>
            @else
                <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Jina</th>
                            <th>Vitendo</th>
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
                                    <div class="action-buttons">
                                        <a href="{{ route('members.dashboard', $parent) }}" class="btn btn-xs btn-primary" title="Angalia Dashibodi">
                                            <i class="fas fa-tachometer-alt"></i>
                                        </a>
                                        <a href="{{ route('members.edit', $parent) }}" class="btn btn-xs btn-warning" title="Hariri">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('members.destroy', $parent) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kumfuta mwanzilishi huyu? Hii itamwondoa kwenye mfumo.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Futa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>
@stop

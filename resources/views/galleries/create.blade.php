@extends('adminlte::page')

@section('title', 'New Album')

@section('content_header')
    <h1>Create New Album</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('galleries.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Album Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. 2023 Family Reunion">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe this album..."></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Album</button>
                <a href="{{ route('galleries.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

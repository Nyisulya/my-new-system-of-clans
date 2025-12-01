@extends('adminlte::page')

@section('title', 'Edit Announcement')

@section('content_header')
    <h1>Edit Announcement</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('announcements.update', $announcement) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ $announcement->title }}">
                </div>
                
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="4" required>{{ $announcement->content }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="info" {{ $announcement->type == 'info' ? 'selected' : '' }}>Info (Blue)</option>
                                <option value="success" {{ $announcement->type == 'success' ? 'selected' : '' }}>Success (Green)</option>
                                <option value="warning" {{ $announcement->type == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                <option value="danger" {{ $announcement->type == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ $announcement->start_date->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Announcement</button>
                <a href="{{ route('announcements.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

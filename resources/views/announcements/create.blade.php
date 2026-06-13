@extends('adminlte::page')

@section('title', 'New Announcement')

@section('content_header')
    <h1>New Announcement</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('announcements.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="Enter title">
                </div>
                
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="4" required placeholder="Enter announcement content"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="info">Info (Blue)</option>
                                <option value="success">Success (Green)</option>
                                <option value="warning">Warning (Yellow)</option>
                                <option value="danger">Danger (Red)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control">
                            <small class="text-muted">Leave blank for indefinite display</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Announcement</button>
                <a href="{{ route('announcements.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

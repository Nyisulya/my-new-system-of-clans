@extends('adminlte::page')

@section('title', 'Edit Campaign')

@section('content_header')
    <h1>Edit Campaign</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('campaigns.update', $campaign) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Campaign Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ $campaign->title }}">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $campaign->description }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Target Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tsh</span>
                                </div>
                                <input type="number" name="target_amount" class="form-control" required min="0" step="1000" value="{{ $campaign->target_amount }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ $campaign->start_date->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ $campaign->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="completed" {{ $campaign->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Campaign</button>
                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

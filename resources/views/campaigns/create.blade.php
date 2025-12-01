@extends('adminlte::page')

@section('title', 'New Campaign')

@section('content_header')
    <h1>New Campaign</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Campaign Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. John's Wedding Fund">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the purpose of this campaign..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Target Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tsh</span>
                                </div>
                                <input type="number" name="target_amount" class="form-control" required min="0" step="1000">
                            </div>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Campaign</button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

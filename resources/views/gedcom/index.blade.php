@extends('adminlte::page')

@section('title', 'GEDCOM Import/Export')

@section('content_header')
    <h1><i class="fas fa-file-export"></i> GEDCOM Import/Export</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-download"></i> Export Family Tree</h3>
                </div>
                <div class="card-body">
                    <p>Download your family tree data in the standard GEDCOM format, compatible with most genealogy software.</p>
                    
                    <form action="{{ route('gedcom.export') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Select Clan to Export</label>
                            <select name="clan_id" class="form-control" required>
                                @foreach($clans as $clan)
                                    <option value="{{ $clan->id }}">{{ $clan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-download"></i> Download GEDCOM File
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-upload"></i> Import GEDCOM</h3>
                </div>
                <div class="card-body">
                    <p>Import data from other genealogy software. <span class="badge badge-warning">Beta</span></p>
                    
                    <form action="{{ route('gedcom.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Select Clan to Import Into</label>
                            <select name="clan_id" class="form-control" required>
                                @foreach($clans as $clan)
                                    <option value="{{ $clan->id }}">{{ $clan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>GEDCOM File (.ged)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="gedcom_file" name="gedcom_file" required>
                                <label class="custom-file-label" for="gedcom_file">Choose file</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@stop

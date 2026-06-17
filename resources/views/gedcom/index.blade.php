@extends('layouts.app')

@section('title', 'Pakia/Pakua GEDCOM')

@section('content_header')
    <h1><i class="fas fa-file-export"></i> Pakia/Pakua GEDCOM</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-download"></i> Pakua Mti wa Ukoo</h3>
                </div>
                <div class="card-body">
                    <p>Pakua data ya mti wa ukoo wako katika mfumo wa kawaida wa GEDCOM, unaoendana na programu nyingi za ukoo.</p>
                    
                    <form action="{{ route('gedcom.export') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Chagua Ukoo wa Kupakua</label>
                            <select name="clan_id" class="form-control" required>
                                @foreach($clans as $clan)
                                    <option value="{{ $clan->id }}">{{ $clan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-download"></i> Pakua Faili la GEDCOM
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-upload"></i> Pakia GEDCOM</h3>
                </div>
                <div class="card-body">
                    <p>Pakia data kutoka kwenye programu nyingine za ukoo. <span class="badge badge-warning">Majaribio (Beta)</span></p>
                    
                    <form action="{{ route('gedcom.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Chagua Ukoo wa Kupakia</label>
                            <select name="clan_id" class="form-control" required>
                                @foreach($clans as $clan)
                                    <option value="{{ $clan->id }}">{{ $clan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Faili la GEDCOM (.ged)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="gedcom_file" name="gedcom_file" required>
                                <label class="custom-file-label" for="gedcom_file">Chagua faili</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-upload"></i> Pakia Data
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

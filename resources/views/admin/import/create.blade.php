@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.import.title') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.import.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="random_filename" value="{{ time() }}" />
            <div class="form-group {{ $errors->has('import_file') ? 'has-error' : '' }}">
                <label for="code">{{ trans('global.import.import_file') }}*</label>
                <br />
                <input type="file" id="import_file" name="import_file" >
                @if($errors->has('import_file'))
                    <em class="invalid-feedback">
                        {{ $errors->first('import_file') }}
                    </em>
                @endif
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection
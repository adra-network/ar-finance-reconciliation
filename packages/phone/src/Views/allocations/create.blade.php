@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('global.allocations.title_singular') }}
        </div>

        <div class="card-body">
            <allocation-form :save-button="true" :redirect-after-save="'{{ route('phone.allocations.index') }}'"></allocation-form>
        </div>
    </div>

@endsection
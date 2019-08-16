@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('global.phone_numbers.phone_number') }}
        </div>

        <div class="card-body">
            <form action="{{ route("phone.caller-numbers.update", [$phoneNumber->id]) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                    <label for="phone_number">{{ trans('global.phone_numbers.phone_number') }}</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control"
                           value="{{ old('phone_number', $phoneNumber->phone_number) }}"
                           disabled>
                    @if($errors->has('phone_number'))
                        <em class="invalid-feedback">
                            {{ $errors->first('phone_number') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.phone_numbers.phone_number_helper') }}
                    </p>
                </div>
                <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                    <label for="user">{{ trans('global.phone_numbers.user') }}</label>
                    <select id="user_id" name="user_id" class="form-control">
                        @foreach($users as $user)
                            <option {{ $phoneNumber->user_id === $user->id ? 'selected' : null }} value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('user'))
                        <em class="invalid-feedback">
                            {{ $errors->first('user') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.phone_numbers.phone_number_helper') }}
                    </p>
                </div>
                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>

@endsection
@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('global.account.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("account.accounts.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                    <label for="code">{{ trans('global.account.fields.code') }}*</label>
                    <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($account) ? $account->code : '') }}">
                    @if($errors->has('code'))
                        <em class="invalid-feedback">
                            {{ $errors->first('code') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.account.fields.code_helper') }}
                    </p>
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">{{ trans('global.account.fields.email') }}</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($account) ? $account->email : '') }}">
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.account.fields.email_helper') }}
                    </p>
                </div>
                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>

@endsection
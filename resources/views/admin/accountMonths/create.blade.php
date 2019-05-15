@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.accountMonth.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.account-months.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('account_id') ? 'has-error' : '' }}">
                <label for="account">{{ trans('global.accountMonth.fields.account') }}*</label>
                <select name="account_id" id="account" class="form-control select2">
                    @foreach($accounts as $id => $account)
                        <option value="{{ $id }}" {{ (isset($accountMonth) && $accountMonth->account ? $accountMonth->account->id : old('account_id')) == $id ? 'selected' : '' }}>{{ $account }}</option>
                    @endforeach
                </select>
                @if($errors->has('account_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('account_id') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('month_date') ? 'has-error' : '' }}">
                <label for="month_date">{{ trans('global.accountMonth.fields.month_date') }}*</label>
                <input type="text" id="month_date" name="month_date" class="form-control date" value="{{ old('month_date', isset($accountMonth) ? $accountMonth->month_date : '') }}">
                @if($errors->has('month_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('month_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.accountMonth.fields.month_date_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('beginning_balance') ? 'has-error' : '' }}">
                <label for="beginning_balance">{{ trans('global.accountMonth.fields.beginning_balance') }}*</label>
                <input type="number" id="beginning_balance" name="beginning_balance" class="form-control" value="{{ old('beginning_balance', isset($accountMonth) ? $accountMonth->beginning_balance : '') }}" step="0.01">
                @if($errors->has('beginning_balance'))
                    <em class="invalid-feedback">
                        {{ $errors->first('beginning_balance') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.accountMonth.fields.beginning_balance_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('net_change') ? 'has-error' : '' }}">
                <label for="net_change">{{ trans('global.accountMonth.fields.net_change') }}*</label>
                <input type="number" id="net_change" name="net_change" class="form-control" value="{{ old('net_change', isset($accountMonth) ? $accountMonth->net_change : '') }}" step="0.01">
                @if($errors->has('net_change'))
                    <em class="invalid-feedback">
                        {{ $errors->first('net_change') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.accountMonth.fields.net_change_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('ending_balance') ? 'has-error' : '' }}">
                <label for="ending_balance">{{ trans('global.accountMonth.fields.ending_balance') }}*</label>
                <input type="number" id="ending_balance" name="ending_balance" class="form-control" value="{{ old('ending_balance', isset($accountMonth) ? $accountMonth->ending_balance : '') }}" step="0.01">
                @if($errors->has('ending_balance'))
                    <em class="invalid-feedback">
                        {{ $errors->first('ending_balance') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.accountMonth.fields.ending_balance_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('export_date') ? 'has-error' : '' }}">
                <label for="export_date">{{ trans('global.accountMonth.fields.export_date') }}</label>
                <input type="text" id="export_date" name="export_date" class="form-control date" value="{{ old('export_date', isset($accountMonth) ? $accountMonth->export_date : '') }}">
                @if($errors->has('export_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('export_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.accountMonth.fields.export_date_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection
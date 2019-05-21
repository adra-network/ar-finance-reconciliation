@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.transaction.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.transactions.update", [$transaction->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('account_id') ? 'has-error' : '' }}">
                <label for="account">{{ trans('global.transaction.fields.account') }}*</label>
                <select name="account_id" id="account" class="form-control select2">
                    @foreach($accounts as $id => $account)
                        <option value="{{ $id }}" {{ (isset($transaction) && $transaction->account ? $transaction->account->id : old('account_id')) == $id ? 'selected' : '' }}>{{ $account }}</option>
                    @endforeach
                </select>
                @if($errors->has('account_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('account_id') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('transaction_date') ? 'has-error' : '' }}">
                <label for="transaction_date">{{ trans('global.transaction.fields.transaction_date') }}*</label>
                <input type="text" id="transaction_date" name="transaction_date" class="form-control date" value="{{ old('transaction_date', isset($transaction) ? $transaction->transaction_date : '') }}">
                @if($errors->has('transaction_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('transaction_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.transaction_date_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                <label for="code">{{ trans('global.transaction.fields.code') }}*</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($transaction) ? $transaction->code : '') }}">
                @if($errors->has('code'))
                    <em class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.code_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('journal') ? 'has-error' : '' }}">
                <label for="journal">{{ trans('global.transaction.fields.journal') }}</label>
                <input type="text" id="journal" name="journal" class="form-control" value="{{ old('journal', isset($transaction) ? $transaction->journal : '') }}">
                @if($errors->has('journal'))
                    <em class="invalid-feedback">
                        {{ $errors->first('journal') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.journal_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('reference') ? 'has-error' : '' }}">
                <label for="reference">{{ trans('global.transaction.fields.reference') }}</label>
                <input type="text" id="reference" name="reference" class="form-control" value="{{ old('reference', isset($transaction) ? $transaction->reference : '') }}">
                @if($errors->has('reference'))
                    <em class="invalid-feedback">
                        {{ $errors->first('reference') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.reference_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('debit_amount') ? 'has-error' : '' }}">
                <label for="debit_amount">{{ trans('global.transaction.fields.debit_amount') }}</label>
                <input type="number" id="debit_amount" name="debit_amount" class="form-control" value="{{ old('debit_amount', isset($transaction) ? $transaction->debit_amount : '') }}" step="0.01">
                @if($errors->has('debit_amount'))
                    <em class="invalid-feedback">
                        {{ $errors->first('debit_amount') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.debit_amount_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('credit_amount') ? 'has-error' : '' }}">
                <label for="credit_amount">{{ trans('global.transaction.fields.credit_amount') }}</label>
                <input type="number" id="credit_amount" name="credit_amount" class="form-control" value="{{ old('credit_amount', isset($transaction) ? $transaction->credit_amount : '') }}" step="0.01">
                @if($errors->has('credit_amount'))
                    <em class="invalid-feedback">
                        {{ $errors->first('credit_amount') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.credit_amount_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                <label for="comment">{{ trans('global.transaction.fields.comment') }}</label>
                <textarea id="comment" name="comment" class="form-control ">{{ old('comment', isset($transaction) ? $transaction->comment : '') }}</textarea>
                @if($errors->has('comment'))
                    <em class="invalid-feedback">
                        {{ $errors->first('comment') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.transaction.fields.comment_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label for="status">{{ trans('global.transaction.fields.status') }}</label>
                <select id="status" name="status" class="form-control">
                    <option value="" disabled {{ old('status', null) === null ? 'selected' : '' }}>@lang('global.pleaseSelect')</option>
                    @foreach(App\AccountTransaction::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $transaction->status) === (string)$key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <em class="invalid-feedback">
                        {{ $errors->first('status') }}
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
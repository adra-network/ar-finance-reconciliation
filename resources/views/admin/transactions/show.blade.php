@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('global.transaction.title') }}
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.account') }}
                    </th>
                    <td>
                        {{ $transaction->account->code ?? '' }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.transaction_date') }}
                    </th>
                    <td>
                        {{ $transaction->transaction_date }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.code') }}
                    </th>
                    <td>
                        {{ $transaction->code }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.journal') }}
                    </th>
                    <td>
                        {{ $transaction->journal }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.reference') }}
                    </th>
                    <td>
                        {{ $transaction->reference }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.debit_amount') }}
                    </th>
                    <td>
                        ${{ $transaction->debit_amount }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.credit_amount') }}
                    </th>
                    <td>
                        ${{ $transaction->credit_amount }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.comment') }}
                    </th>
                    <td>
                        {!! $transaction->comment !!}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.transaction.fields.status') }}
                    </th>
                    <td>
                        {{ App\AccountTransaction::STATUS_SELECT[$transaction->status] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
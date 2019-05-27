@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <div class="row">
            <div class="col-sm-3">
                <select name="account_select" id="account_select"
                        class="check-after-change form-control form-control-sm">
                    <option value="0">-- {{ trans('global.account.choose_account') }} --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}"@if($account->id == $selected_account_id) selected @endif>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <select name="month_select" id="month_select" class="check-after-change form-control form-control-sm">
                    <option value="">-- {{ trans('global.account.choose_month') }} --</option>
                    @foreach ($years_months as $key=>$value)
                        <option value="{{ $value }}"@if($value == $selected_month) selected @endif>{{ $key }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @if($transactions != NULL)
        <div class="card">
            <div class="card-header">
                {{ trans('global.transaction.account') }}
            </div>

            <div class="card-body">
                @if(!$monthly_summaries->isEmpty())
                    <div class="d-flex align-items-end flex-column">
                        <div class="col-sm-2 text-right">
                            <b>{{trans('global.account_page.beginning_balance')}}
                                :</b> {{ number_format($monthly_summaries->beginning_balance, 2) }}
                        </div>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>{{ trans('global.transaction.fields.transaction_date') }}</th>
                            <th>{{ trans('global.transaction.fields.transaction_id') }}</th>
                            <th>{{ trans('global.transaction.fields.journal') }}</th>
                            <th>{{ trans('global.transaction.fields.reference') }}</th>
                            <th>{{ trans('global.transaction.fields.debit_amount') }}</th>
                            <th>{{ trans('global.transaction.fields.credit_amount') }}</th>
                            <th>{{ trans('global.transaction.fields.comment') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ date("m/d/Y", strtotime($transaction->transaction_date)) }}</td>
                                <td>{{ $transaction->code }}</td>
                                <td>{{ $transaction->journal }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td class="td-debit">{{ number_format($transaction->debit_amount, 2) }}</td>
                                <td class="td-credit">{{ number_format($transaction->credit_amount, 2) }}</td>
                                <td>{{ $transaction->comment }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if(!$monthly_summaries->isEmpty())
                    <div class="d-flex align-items-end flex-column">
                        <div class="col-sm-2 text-right">
                            <b>{{trans('global.account_page.net_change')}}
                                :</b> {{ number_format($monthly_summaries->net_change, 2) }}
                        </div>
                        <div class="col-sm-2 text-right">
                            <b>{{trans('global.account_page.ending_balance')}}
                                :</b> {{ number_format($monthly_summaries->ending_balance, 2) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(".check-after-change").change(function () {
            var account_value = $("#account_select").val();
            var month_value = $("#month_select").val();

            if (account_value != 0 && month_value != "")
                window.location = "{{ url('/admin/transactions/account') }}?account_id=" + account_value + "&month=" + month_value;
        });
    </script>
@endsection

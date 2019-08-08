@if($table1)
    <div class="card">
        <div class="card-header">
            {{ trans('global.transaction.account') }}
        </div>

        <div class="card-body">
            @if($table1->monthlySummary)
                <div class="row">
                    <div class="col text-right">
                        <b>{{trans('global.account_page.beginning_balance')}}:</b> {{ number_format($table1->monthlySummary->beginning_balance, 2) }}
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
                    @foreach($table1->transactions as $transaction)
                        <tr>
                            <td>{{ date("m/d/Y", strtotime($transaction->transaction_date)) }}</td>
                            <td>{{ $transaction->code }}</td>
                            <td>{{ $transaction->journal }}</td>
                            <td>{{ $transaction->reference }}</td>
                            <td class="td-debit text-right">{{ number_format($transaction->debit_amount, 2) }}</td>
                            <td class="td-credit text-right">{{ number_format($transaction->credit_amount, 2) }}</td>
                            <td>{{ $transaction->comment }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
{{--                    <a class="btn btn-primary btn-sm"--}}
{{--                       href="{{ route('account.transactions.export', ['account_id' => $account->id, 'month' => $selectedMonth]) }}">{{ trans('global.excel_export') }}</a>--}}
                    <a class="btn btn-primary btn-sm"
                       href="{{ route('account.transactions.export', ['account_id' => $account->id, 'month' => $selectedMonth, 'unallocated-only' => true]) }}">{{ trans('global.excel_export_uallocated') }}</a>
                    @if($account->email)
                        <a class="btn btn-primary btn-sm"
                           href="{{ route('account.transactions.export', ['account_id' => $account->id, 'month' => $selectedMonth, 'email' => 1]) }}">{{ trans('global.send_email') }}</a>
                    @else
                        <div class="row">
                            <div class="col">
                                {{ trans('global.account.no_email') }}. <a href="{{ route('account.accounts.edit', $account->id) }}">[{{ trans('global.edit_here') }}]</a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col">
                    @if($table1->monthlySummary)
                        <div class="row">
                            <div class="col text-right">
                                <b>{{trans('global.account_page.net_change')}}:</b> {{ number_format($table1->monthlySummary->net_change, 2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-right">
                                <b>{{trans('global.account_page.ending_balance')}}:</b> {{ number_format($table1->monthlySummary->ending_balance, 2) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
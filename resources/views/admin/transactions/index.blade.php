@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{{ trans('global.transaction.show_previous_reconciliations') }}</label>
                        <input class="with-previous-months" type="checkbox" {{ request()->query('withPreviousMonths', false) ? 'checked' : null }}>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Reconciled</th>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Comment</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $account)

                        <tr>
                            <td style="font-weight: bold;">
                                {{ $account->name }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        @foreach($account->reconciliations as $reconciliation)
                            <tr>
                                <td></td>
                                <td style="font-weight: bold;">{{ $reconciliation->uuid }}</td>
                                <td>{{ $reconciliation->created_at->format('m/d/Y') }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ trailing_zeros($reconciliation->getTransactionsTotal()) }}</td>
                                <td></td>
                                <td></td>
                            </tr>

                            @foreach($reconciliation->transactions as $transaction)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $transaction->transaction_date }}</td>
                                    <td>{{ $transaction->code }}</td>
                                    <td>{{ $transaction->reference }}</td>
                                    <td>{{ trailing_zeros($transaction->getCreditOrDebit()) }}</td>
                                    <td>{{ $transaction->comment }}</td>
                                    <td>
                                        <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                                    </td>
                                </tr>
                            @endforeach

                        @endforeach

                        <tr>
                            <td></td>
                            <td style="font-weight: bold;">Un-Allocated</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($unreconciledTransactions as $transaction)
                            <tr>
                                <td></td>
                                <td></td>
                                <td>{{ $transaction->transaction_date }}</td>
                                <td>{{ $transaction->code }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td>{{ trailing_zeros($transaction->getCreditOrDebit()) }}</td>
                                <td>{{ $transaction->comment }}</td>
                                <td>
                                    <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td style="font-weight: bold;">
                                {{ $account->name }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;">Closing Balance</td>
                            <td>{{ trailing_zeros($account->getTransactionsTotal()) }}</td>
                            <td></td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <transaction-reconciliation-modal ref="reconciliationModal"></transaction-reconciliation-modal>
@endsection
@section('scripts')
    @parent
    <script>
      $(document).ready(function () {
        $('.with-previous-months').change(function (e) {
          let checked = e.target.checked
          if (checked) {
            window.location = '{{ route('admin.transactions.index', ['withPreviousMonths' => 2]) }}'
          } else {
            window.location = '{{ route('admin.transactions.index') }}'
          }
        })
      })
    </script>
@endsection

{{--<transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>--}}

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
        @foreach($batchTable->accounts as $account)

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
                    <td>{{ number_format($reconciliation->getTransactionsTotal(), 2) }}</td>
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
                        <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                        <td>{{ $transaction->comment }}</td>
                        <td>
                            <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                        </td>
                    </tr>
                @endforeach

            @endforeach

            @foreach($batchTable->transactionGroups as $reference_id => $transactions)
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">Suggested Group {{ $reference_id }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <transaction-reconciliation-button :reference_id="'{{ $reference_id }}'"></transaction-reconciliation-button>
                    </td>
                </tr>
                @foreach($transactions as $transaction)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->code }}</td>
                        <td>{{ $transaction->reference }}</td>
                        <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
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
            @foreach($batchTable->unallocatedTransactions as $transaction)
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ $transaction->transaction_date }}</td>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ $transaction->reference }}</td>
                    <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                    <td>{{ $transaction->comment }}</td>
                    <td>
                        <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold;">Closing Balance</td>
                <td>{{ number_format($account->getTransactionsTotal(), 2) }}</td>
                <td></td>
            </tr>
            @if(isset($batchTable->showVariance))
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold;">Variance</td>
                    <td>{{ number_format($account->getVariance(), 2) }}</td>
                    <td></td>
                </tr>
            @endif

        @endforeach
        </tbody>
    </table>
</div>
<transaction-reconciliation-modal ref="reconciliationModal"></transaction-reconciliation-modal>

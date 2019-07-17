<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
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
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($batchTable->accounts as $account)

            <tr>
                <td style="font-weight: bold;">
                    {{ str_limit_reverse($account->name, 30) }}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @foreach($account->getBatchTableReconciliations() as $reconciliation)
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">{{ Illuminate\Support\Str::limit($reconciliation->uuid, 8) }}</td>
                    <td>{{ $reconciliation->created_at->format('m/d/Y') }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($reconciliation->getTotalTransactionsAmount(), 2) }}</td>
                    <td>{{ $reconciliation->comment }}</td>
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
                        <td>
                            <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
                        </td>
                    </tr>
                @endforeach

            @endforeach

            @foreach($account->getUnallocatedTransactionGroups() as $reference_id => $transactions)
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">Auto: {{ $reference_id }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <transaction-reconciliation-button :reference_id="'{{ $reference_id }}'" :account_id="'{{ $account->id }}'"></transaction-reconciliation-button>
                    </td>
                    <td>
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
                        <td>
                            <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
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
                <td></td>
            </tr>
            @foreach($account->getUnallocatedTransactionsWithoutGrouping() as $transaction)
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
                    <td>
                        <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
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
                <td>{{ number_format($account->getTotalTransactionsAmount(), 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold;">Variance</td>
                <td>{{ number_format($account->getVariance(), 2) }}</td>
                <td></td>
                <td></td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
<transaction-reconciliation-modal ref="ReconciliationModal"></transaction-reconciliation-modal>
<transaction-comment-modal ref="TransactionCommentModal"></transaction-comment-modal>

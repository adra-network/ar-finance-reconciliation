<div class="row">
    @if(isset($showLateTransactions))
        <div class="col">
            @php($lateTransactions = getLateTransactions())
            @if($lateTransactions->count() > 0)
                <h4>Late transactions</h4>
                @foreach($lateTransactions as $transaction)

                    <div class="alert {{ $transaction->getInterval()->alertClass }}">
                        {{ $transaction->transaction_date }} - {{ $transaction->code }} - {{ $transaction->reference }}
                        ({{ $transaction->credit_amount > 0 ? '-$' . number_format($transaction->credit_amount, 2) : '$' . number_format($transaction->debit_amount, 2) }})
                    </div>

                @endforeach
            @endif

        </div>
    @endif
    @if(isset($showUnsyncedSummaries) && count($accounts = getAccountsWithUnsyncedSummaries()) > 0)
        <div class="col">
            <h4>Summaries out of sync after last import</h4>
            @foreach($accounts as $account)
                <div class="alert alert-warning">{{ $account->name }}</div>
            @endforeach
        </div>
    @endif
</div>

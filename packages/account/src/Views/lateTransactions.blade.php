{{--
20 Jan 2020: for now lets hide them, we will bring that back at a later stage when we start driving users to log on directly

<div class="row">
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
</div>
--}}

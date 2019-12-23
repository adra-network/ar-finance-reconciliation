<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>

<div style="margin-bottom: 40px;">
    <div style="float:left;">
        <img src="{{ asset('logos/horz/logo.png') }}" alt="a" style="height:auto; width: 200px;">
        <div>12501 Old Columbia Pike</div>
        <div>Silver Spring, MD 20904</div>
        <div>Office: 301.680.6830</div>
        <div>Fax: 301.680.6870</div>
    </div>
    <div style="float:right;">{{ now()->format('m/d/Y') }}</div>
    <div style="clear:both;"></div>
</div>

<table>
    <tbody>
    <tr>
        <th>Account type</th>
        <td>Employee Accounts Receivable</td>
    </tr>
    <tr>
        <th>Account Name</th>
        <td>A/R - {{ $account->getNameOnly() }}</td>
    </tr>
    <tr>
        <th>Account number</th>
        <td>{{ $account->code }}</td>
    </tr>
    </tbody>
</table>

<table style="margin-top:40px;">
    <thead>
    <tr>
        <th>Status</th>
        <th>Date</th>
        <th>Transaction id</th>
        <th>Reference</th>
        <th>Amount</th>
        <th>Comments</th>
    </tr>
    </thead>
    <tbody>

    <?php
    /** @var Account\Models\Account $account */
    $account = $batchTable->accounts->first();
    ?>
    @foreach ($account->getBatchTableReconciliations() as $reconciliation)
        <tr>
            <td><b>{{ $reconciliation->isFullyReconciled() ? 'Cleared' : 'Partialy cleared' }}</b></td>
            <td>{{ $reconciliation->created_at->format('m/d/Y') }}</td>
            <td></td>
            <td></td>
            <td>{{ number_format($reconciliation->getTotalTransactionsAmount(), 2) }}</td>
            <td></td>
        </tr>

        @foreach ($reconciliation->transactions as $transaction) {
        <tr>
            <td style="text-align:right;">{{ optional($intervals->getIntervalByTransaction($transaction))->stars }}</td>
            <td>{{ $transaction->transaction_date }}</td>
            <td>{{ $transaction->code }}</td>
            <td>{{ $transaction->reference }}</td>
            <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
            <td>{{ $transaction->comment }}</td>
        </tr>
        @endforeach
        @foreach ($account->getUnallocatedTransactionGroups() as $reference_id => $transactions)

            <tr>
                <td>{{ 'Auto' . $reference_id }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @foreach ($transactions as $transaction)
                <tr>
                    <td style="text-align:right;">{{ optional($intervals->getIntervalByTransaction($transaction))->stars }}</td>
                    <td>{{ $transaction->transaction_date }}</td>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ $transaction->reference }}</td>
                    <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                    <td>{{ $transaction->comment }}</td>
                </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="4"></td>
            <th><b>Sub-Total</b></th>
            <th>{{ number_format($reconciliation->getTotalTransactionsAmount(), 2) }}</th>
        </tr>
    @endforeach


    <tr>
        <th><b>Uncleared</b></th>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    @foreach ($account->getUnallocatedTransactionsWithoutGrouping() as $transaction)
        <tr>
            <td style="text-align:right;">{{ optional($intervals->getIntervalByTransaction($transaction))->stars }}</td>
            <td>{{ $transaction->transaction_date }}</td>
            <td>{{ $transaction->code }}</td>
            <td>{{ $transaction->reference }}</td>
            <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
            <td>{{ $transaction->comment }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4"></td>
        <th><b>Sub-Total</b></th>
        <th>{{ number_format($account->getUnallocatedTransactionsWithoutGroupingTotal(), 2) }}</th>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <th><b>Total</b></th>
        <th>{{ number_format($account->getTotalTransactionsAmount(), 2) }}</th>
    </tr>
    </tbody>
</table>

<div style="margin-top:50px;">
    @foreach($intervals->getIntervals() as $key => $interval)
        <div>{{ $interval->stars . ' ' . $interval->pdfText }}</div>
    @endforeach
</div>

</body>
</html>

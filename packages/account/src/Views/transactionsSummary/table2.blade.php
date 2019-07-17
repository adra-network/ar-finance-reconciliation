@if(isset($table2->transactions))
    <div class="card">
        <div class="card-header">
            {{ trans('global.transaction.account') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Comment</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($table2->transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>{{ $transaction->code }}</td>
                            <td>{{ $transaction->reference }}</td>
                            <td>{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                            <td>{{ $transaction->comment }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">Amount</td>
                        <td>{{ number_format($table2->amount, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">Variance</td>
                        <td>{{ number_format($table2->variance, 2) }}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
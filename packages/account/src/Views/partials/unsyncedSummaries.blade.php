@if(count($summaries = getUnsyncedSummariesWithAccounts()) > 0)
    <div class="row">
        <div class="col">
            <h4>Summaries out of sync after last import</h4>
            <div class="alert alert-warning">
                @foreach($summaries as $summary)
                    - {{ $summary->account->name }}
                    (ending balance: ${{ number_format($summary->checker->currentBalance / 100, 2) }},
                    new opening balance: ${{ number_format($summary->checker->beginningBalance / 100, 2) }},
                    difference ${{ number_format($summary->checker->diff() / 100, 2) }})<br />
                @endforeach
                <a href="{{ route('account.reports.summaries-out-of-sync') }}">View full report</a>
            </div>
        </div>
    </div>
@endif

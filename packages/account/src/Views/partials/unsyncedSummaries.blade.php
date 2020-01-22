@if(count($summaries = getUnsyncedSummariesWithAccounts()) > 0)
    <div class="row">
        <div class="col">
            <h4>Summaries out of sync after last import</h4>
            <div class="alert alert-warning">
                @foreach($summaries as $summary)
                    - {{ $summary->account->name }}<br />
                @endforeach
                <a href="{{ route('account.reports.summaries-out-of-sync') }}">View full report</a>
            </div>
        </div>
    </div>
@endif

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
            @if(!isset($disableButtons))
                <th></th>
                <th></th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($batchTable->accounts as $account)

            <tr class="account-{{$account->id }}">
                <td style="font-weight: bold;">
                    {{ $account->name_formatted }}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @if(!isset($disableButtons))
                    <td></td>
                    <td></td>
                @endif
            </tr>

            @foreach($account->getBatchTableReconciliations($showFullyReconciled, $dateFilter) as $reconciliation)
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">
                        @if($reconciliation->isFullyReconciled())
                            Reconciled
                        @else
                            {{ request()->routeIs('account.transactions.summary') ? "Partially Cleared" : "Partial Reconcile" }}
                        @endif
                    </td>
                    <td>{{ $reconciliation->created_at->format('m/d/Y') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if(!isset($disableButtons))
                        <td>
                            @if($reconciliation->isFullyReconciled())
                                <transaction-reconciliation-button :reconciliation_id="{{ $reconciliation->id }}"></transaction-reconciliation-button>
                            @endif
                        </td>
                        <td></td>
                    @endif
                </tr>

                @foreach($reconciliation->transactions as $transaction)
                    <tr class="transaction-{{ $transaction->id }}">
                        <td></td>
                        <td></td>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->code }}</td>
                        <td>{{ $transaction->reference }}</td>
                        <td class="text-right">{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                        <td>
                        @foreach($transaction->getCommentsByUserAccess(auth()->user(), 'transaction') as $comment)
                            <div> {{ $comment->user->name }} ({{ $comment->created_at->format('j-n-Y g:i a') }}) : {{ $comment->comment }} </div>
                        @endforeach
                        </td>
                        @if(!isset($disableButtons))
                            <td>
                                <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                            </td>
                            <td>
                                <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
                            </td>
                        @endif

                    </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td style="font-weight: bold;">Sub-total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right font-weight-bold">{{ number_format($reconciliation->getTotalTransactionsAmount(), 2) }}</td>
                    <td>
                        @foreach($reconciliation->getCommentsByUserAccess(auth()->user()) as $comment)
                            <div> {{ $comment->user->name }} ({{ $comment->created_at->format('j-n-Y g:i a') }}) : {{ $comment->comment }} </div>
                        @endforeach
                    </td>
                    @if(!isset($disableButtons))
                        <td></td>
                        <td></td>
                    @endif
                </tr>

            @endforeach

            @foreach($account->getUnallocatedTransactionGroups() as $group)
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">Auto: {{ $group->referenceString }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if(!isset($disableButtons))
                        <td>
                            <transaction-reconciliation-button :reference_id="'{{ $group->referenceString }}'" :reference-type="'{{ $group->type }}'" :account_id="'{{ $account->id }}'"></transaction-reconciliation-button>
                        </td>
                        <td></td>
                    @endif

                </tr>
                @foreach($group as $transaction)
                    <tr class="transaction-{{ $transaction->id }}">
                        <td></td>
                        <td></td>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->code }}</td>
                        <td>{{ $transaction->reference }}</td>
                        <td class="text-right">{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                        <td>
                            @foreach($transaction->getCommentsByUserAccess(auth()->user(), 'transaction') as $comment)
                                <div> {{ $comment->user->name }} ({{ $comment->created_at->format('j-n-Y g:i a') }}) : {{ $comment->comment }} </div>
                            @endforeach
                        </td>
                        @if(!isset($disableButtons))
                            <td>
                                <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                            </td>
                            <td>
                                <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
                            </td>
                        @endif

                    </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td style="font-weight: bold;">Sub-total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right font-weight-bold">{{ number_format($group->getGroupTotal(), 2) }}</td>
                    <td></td>
                    @if(!isset($disableButtons))
                        <td></td>
                        <td></td>
                    @endif
                </tr>

            @endforeach

            <tr>
                <td></td>
                <td style="font-weight: bold;">{{ request()->routeIs('account.transactions.summary') ? 'Uncleared' : 'Un-Reconciled' }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right font-weight-bold">
                    {{--                    {{ number_format($account->getUnallocatedTransactionsWithoutGroupingTotal(), 2) }}--}}
                </td>
                <td></td>
                @if(!isset($disableButtons))
                    <td></td>
                    <td></td>
                @endif
            </tr>
            @foreach($account->getUnallocatedTransactionsWithoutGrouping() as $transaction)
                <tr class="transaction-{{ $transaction->id }}">
                    <td></td>
                    <td></td>
                    <td>{{ $transaction->transaction_date }}</td>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ $transaction->reference }}</td>
                    <td class="text-right">{{ number_format($transaction->getCreditOrDebit(), 2) }}</td>
                    <td>
                        @foreach($transaction->getCommentsByUserAccess(auth()->user(), 'transaction') as $comment)
                            <div> {{ $comment->user->name }} ({{ $comment->created_at->format('j-n-Y g:i a') }}) : {{ $comment->comment }} </div>
                        @endforeach
                    </td>
                    @if(!isset($disableButtons))
                        <td>
                            <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                        </td>
                        <td>
                            <transaction-comment-modal-button :transaction_id="{{ $transaction->id }}"></transaction-comment-modal-button>
                        </td>
                    @endif

                </tr>
            @endforeach

            <tr>
                <td></td>
                <td style="font-weight: bold;">Sub-total</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($account->getUnreconciledTransactionsSubtotal(), 2) }}</td>
                <td></td>
                @if(!isset($disableButtons))
                    <td></td>
                    <td></td>
                @endif
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold;">Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($account->getTotalTransactionsAmount(), 2) }}</td>
                <td></td>
                @if(!isset($disableButtons))
                    <td></td>
                    <td></td>
                @endif
            </tr>

            <tr>
                <td></td>
                <td style="font-weight: bold;">Variance</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($account->getVariance(), 2) }}</td>
                <td></td>
                @if(!isset($disableButtons))
                    <td></td>
                    <td></td>
                @endif
            </tr>

        @endforeach
        </tbody>
    </table>
</div>


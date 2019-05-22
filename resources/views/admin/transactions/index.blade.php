@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{ trans('global.transaction.fields.account') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.reconciled') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.transaction_date') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.transaction_id') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.reference') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.comment') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($accounts_transactions as $account_name => $transactions_list)
                        <tr>
                            <td colspan="8">
                                <b>{{ $account_name }}</b>
                            </td>
                        </tr>
                        @foreach ($transactions_list as $transaction)
                            <tr>
                                <td>

                                </td>
                                <td>
                                    ??? {{ $transaction->reconciliation_id ? 'T' : 'F' }}
                                </td>
                                <td>
                                    {{ $transaction->transaction_date ?? '' }}
                                </td>
                                <td>
                                    {{ $transaction->code ?? '' }}
                                </td>
                                <td>
                                    {{ $transaction->reference ?? '' }}
                                </td>
                                <td>
                                    @if ($transaction->debit_amount > 0)
                                        {{ number_format($transaction->debit_amount, 2) }}
                                    @elseif ($transaction->credit_amount > 0)
                                        -{{ number_format($transaction->credit_amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $transaction->comment ?? '' }}
                                </td>
                                <td>
                                    <transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>
                                </td>

                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8">No data in the table.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <transaction-reconciliation-modal ref="reconciliationModal"></transaction-reconciliation-modal>

    </div>
@endsection

@section('scripts')
    @parent
    <script>
      $(document).ready(function () {
          $('.open-reconciliation-modal').click(function() {
            let id = $(this).attr('data-transaction-id')
          })
      });
      $(function () {
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
          text: deleteButtonTrans,
          url: "{{ route('admin.transactions.massDestroy') }}",
          className: 'btn-danger',
          action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {
              return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
              alert('{{ trans('global.datatables.zero_selected') }}')

              return
            }

            if (confirm('{{ trans('global.areYouSure') }}')) {
              $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: config.url,
                data: {ids: ids, _method: 'DELETE'}
              })
                .done(function () {
                  location.reload()
                })
            }
          }
        }
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('transaction_delete')
            dtButtons.push(deleteButton)
        @endcan

        $('.datatable:not(.ajaxTable)').DataTable({buttons: dtButtons})
      })

    </script>
@endsection
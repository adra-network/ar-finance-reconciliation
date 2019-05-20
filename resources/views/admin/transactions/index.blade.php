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
                                ???
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
                                <a href="#" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fas fa-cogs">

                                    </i>
                                </a>
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
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reconcile Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>
                            {{ trans('global.transaction.fields.reference') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.amount') }}
                        </th>
                    </tr>
                    <tr>
                        <td>
                            05/01/2019 - AA123
                        </td>
                        <td>
                            $10.00
                        </td>
                    </tr>
                    <tr>
                        <th>Running total:</th>
                        <th>$10.00</th>
                    </tr>
                </table>
                <h4>Choose From Unreconciled Transactions</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>
                            {{ trans('global.transaction.fields.reference') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.select') }}
                        </th>
                    </tr>
                    <tr>
                        <td>
                            02/01/2019 - le-roux X (23-45678)
                        </td>
                        <td>
                            $250.15
                        </td>
                        <td>
                            <input type="button" class="btn btn-sm btn-primary" value="Add to list" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            03/01/2019 - le-roux Z (12-987654)
                        </td>
                        <td>
                            -$25.15
                        </td>
                        <td>
                            <input type="button" class="btn btn-sm btn-primary" value="Add to list" />
                        </td>
                    </tr>
                </table>
                Comments:
                <br />
                <textarea name="comments" rows="3" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.transactions.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
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
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('transaction_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
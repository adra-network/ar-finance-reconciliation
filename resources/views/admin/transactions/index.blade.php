@extends('layouts.admin')
@section('content')
@can('transaction_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.transactions.create") }}">
                {{ trans('global.add') }} {{ trans('global.transaction.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.transaction.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.transaction.fields.account') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.transaction_date') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.code') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.journal') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.reference') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.debit_amount') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.credit_amount') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.comment') }}
                        </th>
                        <th>
                            {{ trans('global.transaction.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $key => $transaction)
                        <tr data-entry-id="{{ $transaction->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $transaction->account->code ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->transaction_date ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->code ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->journal ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->reference ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->debit_amount ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->credit_amount ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->comment ?? '' }}
                            </td>
                            <td>
                                {{ App\Transaction::STATUS_SELECT[$transaction->status] ?? '' }}
                            </td>
                            <td>
                                @can('transaction_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.transactions.show', $transaction->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('transaction_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.transactions.edit', $transaction->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('transaction_delete')
                                    <form action="{{ route('admin.transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
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
@endsection
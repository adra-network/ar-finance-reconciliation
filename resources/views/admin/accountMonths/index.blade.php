@extends('layouts.admin')
@section('content')
@can('account_month_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.account-months.create") }}">
                {{ trans('global.add') }} {{ trans('global.accountMonth.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.accountMonth.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.account') }}
                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.month_date') }}
                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.beginning_balance') }}
                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.net_change') }}
                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.ending_balance') }}
                        </th>
                        <th>
                            {{ trans('global.accountMonth.fields.export_date') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accountMonths as $key => $accountMonth)
                        <tr data-entry-id="{{ $accountMonth->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $accountMonth->account->code ?? '' }}
                            </td>
                            <td>
                                {{ $accountMonth->month_date ?? '' }}
                            </td>
                            <td>
                                {{ $accountMonth->beginning_balance ?? '' }}
                            </td>
                            <td>
                                {{ $accountMonth->net_change ?? '' }}
                            </td>
                            <td>
                                {{ $accountMonth->ending_balance ?? '' }}
                            </td>
                            <td>
                                {{ $accountMonth->export_date ?? '' }}
                            </td>
                            <td>
                                @can('account_month_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.account-months.show', $accountMonth->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('account_month_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.account-months.edit', $accountMonth->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('account_month_delete')
                                    <form action="{{ route('admin.account-months.destroy', $accountMonth->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.account-months.massDestroy') }}",
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
@can('account_month_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
@endsection
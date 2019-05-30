@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{{ trans('global.transaction.show_previous_reconciliations') }}</label>
                        <input class="with-previous-months" type="checkbox" {{ request()->query('withPreviousMonths', false) ? 'checked' : null }}>
                    </div>
                </div>
            </div>
            @include('admin.transactions.batchTable')
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
      $(document).ready(function () {
        $('.with-previous-months').change(function (e) {
          let checked = e.target.checked
          if (checked) {
            window.location = '{{ route('admin.transactions.index', ['withPreviousMonths' => 2]) }}'
          } else {
            window.location = '{{ route('admin.transactions.index') }}'
          }
        })
      })
    </script>
@endsection

{{--<transaction-reconciliation-button :transaction_id="{{ $transaction->id }}"></transaction-reconciliation-button>--}}

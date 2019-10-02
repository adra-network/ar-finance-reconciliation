@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">
            @include('account::transactionsSummary.header')
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            @include('account::transactionsSummary.table1')
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col">
                    @include('account::transactionsSummary.table2')
                </div>
            </div>
            @if(isset($batchTable))
                <div class="card">
                    <div class="card-header">
                        {{ trans('global.transaction.account') }}
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                @include('account::transactions.batchTable')
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
      $(".check-after-change").change(function () {
        let account_value = $("#account_select").val();
        let import_value = $("#import_select").val();

        if (account_value != 0 && import_value != "")
          window.location = "{{ route('account.transactions.summary') }}?account_id=" + account_value + "&import=" + import_value;
      });
    </script>
@endsection

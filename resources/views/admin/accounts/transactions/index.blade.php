@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">
            @include('admin.accounts.transactions.header')
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            @include('admin.accounts.transactions.table1')
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col">
                    @include('admin.accounts.transactions.table2')
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
                                @include('admin.transactions.batchTable')
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
        var account_value = $("#account_select").val();
        var month_value = $("#month_select").val();

        if (account_value != 0 && month_value != "")
          window.location = "{{ route('admin.account.transactions') }}?account_id=" + account_value + "&month=" + month_value;
      });
    </script>
@endsection

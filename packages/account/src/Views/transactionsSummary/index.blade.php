@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">
            @include('account::transactionsSummary.header')
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Account summary</a></li>
                    <li><a href="#tabs-2">Account detail</a></li>
                </ul>
                <div id="tabs-1">
                    @include('account::transactionsSummary.table1')
                </div>
                <div id="tabs-2">
                    @include('account::transactionsSummary.table2')
                </div>
            </div>
        </div>
        <div class="col">
            @if(isset($batchTable))
                <div class="card">
                    <div class="card-header">
                        Outstanding balance
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                @include('account::transactions.batchTable', ['disableButtons' => true])
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
      $(".check-after-change").change(function () {
        let account_value = $("#account_select").val();
        let import_value = $("#import_select").val();

        if (account_value != 0 && import_value != "")
          window.location = "{{ route('account.transactions.summary') }}?account_id=" + account_value + "&import=" + import_value;
      });

      $(document).ready(function () {
        $("#tabs").tabs();
      })
    </script>
@endsection

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
                    <li><a href="#tab1">Account summary</a></li>
                    <li><a href="#tab2">Account detail</a></li>
                </ul>
                <div id="tab1">
                    @if(isset($batchTable))
                        @include('account::transactions.batchTable', [
                        'disableButtons' => true,
                        'showFullyReconciled' => false,
                        'dateFilter' => [null, null]
                        ])
                    @endif
                </div>
                <div id="tab2">
                    <select name="import_select" id="import_select" class="check-after-change form-control form-control-sm mt-3 mb-3">
                        <option value="">-- Choose import --</option>
                        @foreach ($accountImports as $import)
                            <option value="{{ $import->id }}" {{ (int)$selectedImport === $import->id ? 'selected' : '' }}>{{ $import->title }}</option>
                        @endforeach
                    </select>
                    @include('account::transactionsSummary.table1')
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@endsection

@section('scripts')
    @parent
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
      $(document).ready(function () {
        $("#tabs").tabs()
      })
    </script>
    <script>
      $(document).ready(function () {
        $(".check-after-change").change(function () {
          let account_value = $("#account_select").val()
          let import_value = $("#import_select").val()

          let query = '';
          if (account_value != 0) {
            query += 'account_id=' + account_value
          }
          if (import_value != '') {
            if (account_value) {
              query += '&'
            }
            query += 'import=' + import_value + '#tab2'
          }

          if (query != '') {
            window.location = "{{ route('account.transactions.summary') }}?" + query
          }
        })
      })
    </script>
@endsection

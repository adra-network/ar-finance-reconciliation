@extends('layouts.admin')
@section('content')
    <div id="tabs">
        <ul>
            <li><a href="#tab1">Transaction Summary</a></li>
            <li><a href="#tab2">Transaction Detail</a></li>
        </ul>
        <div id="tab1">
            <div class="mt-3 mb-3">
                <input type="checkbox" value="true" name="showReconciled" {{ request()->query('showReconciled', false) == 'true' ? "checked" : null }}> Show reconciled
                <br>
                {{--                <input type="checkbox" value="true" name="showVariance" {{ request()->query('showVariance', false) == 'true' ? "checked" : null }}> Show variance--}}
            </div>
            @include('account::transactions.batchTable', [
                'batchTable' => $batchTable,
                'showFullyReconciled' => $showFullyReconciled,
                'dateFilter' => [null, null]
            ])
        </div>
        <div id="tab2">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        @include('account::partials.datepicker')
                    </div>
                </div>
            </div>
            @include('account::transactions.batchTable', [
                'batchTable' => $batchTable,
                'showFullyReconciled' => true,
                'dateFilter' => $dateFilter,
            ])
        </div>
    </div>

    <transaction-reconciliation-modal ref="ReconciliationModal"></transaction-reconciliation-modal>
    <transaction-comment-modal ref="TransactionCommentModal"></transaction-comment-modal>
@endsection
@section('scripts')
    @parent

    {{--TABS--}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
      $(document).ready(function () {
        $("#tabs").tabs();
      })
    </script>
    {{--TABS END--}}

    <script>
      $(document).ready(function () {
        let url = window.location.href.split('#');
        if (url[1]) {
          let scrollTo = url[1].split('-')
          let item = scrollTo[0]
          let id = scrollTo[1]
          let scrollTop = $('.' + item + '-' + id).offset().top
          $('html, body').animate({
            scrollTop: scrollTop - 100
          }, 1000);
        }

      })
    </script>

    <script>
      $(document).ready(function () {
        $('[name="showReconciled"], [name="showVariance"]').on('change', function () {
          collectFilterDataAndReloadPage(1)
        })
        $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
          //do something, like clearing an input
          collectFilterDataAndReloadPage(2)
        });


        function collectFilterDataAndReloadPage(tab) {
          let date = $('#date_filter').val();
          let showReconciled = $('[name="showReconciled"]').is(":checked")
          let showVariance = $('[name="showVariance"]').is(":checked")


          let url = window.location.href.split('?')[0]

          let query = []
          if (date) {
            query.push('date_filter=' + date)
          }
          if (showReconciled) {
            query.push('showReconciled=' + showReconciled)
          }
          if (showVariance) {
            query.push('showVariance=' + showVariance)
          }

          let queryString = null
          if (query.length > 0) {
            queryString = '?' + query.join('&') + '#tab' + tab
          }

          window.location = url + (queryString || '')
        }
      })
    </script>
@endsection
@extends('layouts.admin')
@section('content')
    <div id="tabs">
        <ul>
            <li><a href="#tab1">Transaction Summary</a></li>
            <li><a href="#tab2">Transaction Detail</a></li>
        </ul>
        <div id="tab1">
            @include('account::transactions.batchTable')
        </div>
        <div id="tab2">
            <div class="row">
                <div class="col-4">
                    <form action="{{ route('account.transactions.index') . '#tab2' }}">
                        <div class="form-group">
                            @include('account::partials.datepicker')
                        </div>
                        <div class="mt-1 mb-3">
                            <input type="submit" value="Filter" class="btn btn-info">
                        </div>
                    </form>
                </div>
            </div>
            @include('account::transactions.batchTable', ['batchTable' => $batchTableWithPreviousMonths])
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
@endsection
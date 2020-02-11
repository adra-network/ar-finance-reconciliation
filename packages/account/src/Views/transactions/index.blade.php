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
                <div class="row">
                    <div class="col-4">
                        @include('account::partials.datepicker', ['id' => 'date_filter2'])
                    </div>
                </div>

                <br>
                <input type="checkbox" value="true" name="showZeroVariance" {{ request()->query('showZeroVariance', false) == 'true' ? "checked" : null }}> Show 0 variance
            </div>

            @if (isset($batchTable->accountsCount))
                <ul class="pagination" role="navigation">
                    {{-- Previous Page Link --}}
                    @if ($pageNumber == 1)
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ route('account.transactions.index') }}?page={{ $pageNumber-1 }}&{{ $queryParams }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @for ($page=1; $page <= $batchTable->pages; $page++)
                        @if ($page == $pageNumber)
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ route('account.transactions.index') }}?page={{ $page }}&{{ $queryParams }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($pageNumber < $batchTable->pages)
                        <li class="page-item">
                            <a class="page-link"
                               href="{{ route('account.transactions.index') }}?page={{ $pageNumber + 1 }}&{{ $queryParams }}"
                               rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            @endif

            @include('account::transactions.batchTable', [
                'batchTable' => $batchTable,
                'showFullyReconciled' => $showFullyReconciled,
                'dateFilter' => $dateFilter2,
                'showZeroVariance' => $showZeroVariance,
            ])

            @if (isset($batchTable->accountsCount))
                <ul class="pagination" role="navigation">
                    {{-- Previous Page Link --}}
                    @if ($pageNumber == 1)
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ route('account.transactions.index') }}?page={{ $pageNumber-1 }}&{{ $queryParams }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @for ($page=1; $page <= $batchTable->pages; $page++)
                        @if ($page == $pageNumber)
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ route('account.transactions.index') }}?page={{ $page }}&{{ $queryParams }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($pageNumber < $batchTable->pages)
                        <li class="page-item">
                            <a class="page-link"
                               href="{{ route('account.transactions.index') }}?page={{ $pageNumber + 1 }}&{{ $queryParams }}"
                               rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            @endif
        </div>
        <div id="tab2">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        @include('account::partials.datepicker', ['id' => 'date_filter'])
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
          remove_hash_from_url()
        }

      })

      function remove_hash_from_url()
      {
        var uri = window.location.toString();
        if (uri.indexOf("#") > 0) {
          var clean_uri = uri.substring(0, uri.indexOf("#"));
          window.history.replaceState({}, document.title, clean_uri);
        }
      }
    </script>

    <script>
      $(document).ready(function () {
        $('[name="showReconciled"], [name="showZeroVariance"]').on('change', function () {
          collectFilterDataAndReloadPage(1)
        })
        $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
          //do something, like clearing an input
          collectFilterDataAndReloadPage(2)
        });
        $('#date_filter2').on('apply.daterangepicker', function (ev, picker) {
          //do something, like clearing an input
          collectFilterDataAndReloadPage(1)
        });


          function collectFilterDataAndReloadPage(tab) {
          let date = $('#date_filter').val();
          let date2 = $('#date_filter2').val();
          let showReconciled = $('[name="showReconciled"]').is(":checked")
          let showZeroVariance = $('[name="showZeroVariance"]').is(":checked")


          let url = window.location.href.split('?')[0]

          let query = []
          if (date) {
            query.push('date_filter=' + date)
          }
          if (date2) {
            query.push('date_filter2=' + date2)
          }
          if (showReconciled) {
            query.push('showReconciled=' + showReconciled)
          }
          if (showZeroVariance) {
            query.push('showZeroVariance=' + showZeroVariance)
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
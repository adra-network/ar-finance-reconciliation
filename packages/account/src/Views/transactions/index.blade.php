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
            @include('account::transactions.batchTable')
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
            window.location = '{{ route('account.transactions.index', ['withPreviousMonths' => 2]) }}'
          } else {
            window.location = '{{ route('account.transactions.index') }}'
          }
        })


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
@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col">

            <form action="{{ route('account.reports.employee-summary') }}" method="GET">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" class="form-control" name="date_filter" id="date_filter">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" class="btn btn-info" value="filter">
                    </div>
                </div>
            </form>


            @if($summaries)
                <div class="row mt-3">
                    <div class="col">
                        <table class="table-striped table">
                            <tr>
                                <th>Employee</th>
                                @foreach($months as $month)
                                    <th>{{ $month->format('M') }}</th>
                                @endforeach
                            </tr>
                            @foreach($summaries as $summary)
                                <tr>
                                    <td>{{ $summary->account->name }}</td>
                                    @foreach($summary->months as $month)
                                        <td>{{ $month->date->format('M') }}</td>
                                    @endforeach


                                    {{-- FILLERS--}}
{{--                                    @if($summary->months->count() < $maxMonths)--}}
{{--                                        @for($i = 0; $maxMonths - $summary->months->count() > $i; $i++)--}}
{{--                                            <td></td>--}}
{{--                                        @endfor--}}
{{--                                    @endif--}}

                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif


        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css">
    <script>

      $(function () {
        let dateInterval = getQueryParameter('date_filter');
        let start = moment().startOf('isoWeek');
        let end = moment().endOf('isoWeek');

        if (dateInterval) {
          dateInterval = dateInterval.split(' - ');
          start = dateInterval[0];
          end = dateInterval[1];
        }

        $('#date_filter').daterangepicker({
          "showDropdowns": true,
          "showWeekNumbers": true,
          "alwaysShowCalendars": true,
          startDate: start,
          endDate: end,
          locale: {
            format: 'YYYY-MM-DD',
            firstDay: 1,
          },
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            'All time': [moment().subtract(30, 'year').startOf('month'), moment().endOf('month')],
          }
        });
      });

      function getQueryParameter(name) {
        const url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");

        const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);

        if (!results) return null;
        if (!results[2]) return '';

        return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

    </script>
@endsection
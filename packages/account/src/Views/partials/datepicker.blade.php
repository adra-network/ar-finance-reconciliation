@php($id = $id ?? 'date_filter')
<input type="text" class="form-control" name="{{ $id }}" id="{{ $id }}">

@section('scripts')
    @parent
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css">
    <script>

      $(function () {
        let dateInterval = getQueryParameter('{{ $id }}');
        let start = moment().startOf('isoWeek');
        let end = moment().endOf('isoWeek');

        if (dateInterval) {
          dateInterval = dateInterval.split(' - ');
          start = dateInterval[0];
          end = dateInterval[1];
        }

        $('#{{ $id }}').daterangepicker({
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


        // $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
        //   do something, like clearing an input
          // console.log(ev, picker)
        // });


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
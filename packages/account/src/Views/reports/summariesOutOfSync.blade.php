@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-header">
                    Summaries out of sync
                </div>
                <div class="card-body">

                    <table id="table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Account</th>
                            <th>User</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($summaries as $summary)
                            <tr>
                                <td>{{ $summary->account->name_formatted }}</td>
                                <td>{{ optional($summary->account->user)->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">All summaries are in sync</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
      $(document).ready( function () {
        $('#table').DataTable();
      } );
    </script>
@endsection
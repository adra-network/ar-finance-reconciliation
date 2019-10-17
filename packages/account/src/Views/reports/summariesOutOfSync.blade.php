@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-header">
                    Summaries out of sync
                </div>
                <div class="card-body">

                    <table class="table table-striped">

                        <thead>
                        <tr>
                            <th>Account</th>
                            <th>User</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($summaries as $summary)
                            <tr>
                                <td>{{ $summary->account->name }}</td>
                                <td>{{ $summary->account->user->name }}</td>
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
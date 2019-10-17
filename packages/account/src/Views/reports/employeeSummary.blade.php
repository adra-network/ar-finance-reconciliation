@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col">

            <form action="{{ route('account.reports.employee-summary') }}" method="GET">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            @include('account::partials.datepicker')
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" class="btn btn-info" value="filter">
                    </div>
                </div>
            </form>


            @if($accounts)
                <div class="row mt-3">
                    <div class="col">
                        <table class="table-striped table">
                            <tr>
                                <th>Employee</th>
                                @foreach($months as $month)
                                    <th>{{ $month->format('M') }} import</th>
                                    <th>{{ $month->format('M') }} variance</th>
                                @endforeach
                            </tr>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->account->name }}</td>
                                    @foreach($account->summaries as $summary)
                                        <td>{{ $summary->summary->ending_balance }}</td>
                                        <td>{{ $summary->variance }}</td>
                                    @endforeach


                                    {{-- FILLERS--}}
                                    @if($months->count() > $account->summaries->count())
                                        @for($i = 0; ($months->count() - $account->summaries->count()) * 2 > $i; $i++)
                                            <td></td>
                                        @endfor
                                    @endif

                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif


        </div>
    </div>
@endsection
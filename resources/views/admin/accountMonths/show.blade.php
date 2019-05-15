@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('global.accountMonth.title') }}
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.account') }}
                    </th>
                    <td>
                        {{ $accountMonth->account->code ?? '' }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.month_date') }}
                    </th>
                    <td>
                        {{ $accountMonth->month_date }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.beginning_balance') }}
                    </th>
                    <td>
                        ${{ $accountMonth->beginning_balance }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.net_change') }}
                    </th>
                    <td>
                        ${{ $accountMonth->net_change }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.ending_balance') }}
                    </th>
                    <td>
                        ${{ $accountMonth->ending_balance }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.accountMonth.fields.export_date') }}
                    </th>
                    <td>
                        {{ $accountMonth->export_date }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
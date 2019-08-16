@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.phone_numbers.title') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{ trans('global.phone_numbers.phone_number') }}
                        </th>
                        <th>
                            {{ trans('global.phone_numbers.user') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($phoneNumbers as $phoneNumber)
                        <tr>
                            <td>
                                {{ $phoneNumber->phone_number ?? '' }}
                            </td>
                            <td>
                                {{ $phoneNumber->user->name ?? '' }}
                            </td>
                            <td>
                                @can('account_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('phone.caller-numbers.edit', $phoneNumber->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $phoneNumbers->links() }}
        </div>
    </div>
@endsection
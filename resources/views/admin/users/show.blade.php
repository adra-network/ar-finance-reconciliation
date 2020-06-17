@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('global.user.title') }}
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>
                        {{ trans('global.user.fields.name') }}
                    </th>
                    <td>
                        {{ $user->name }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.user.fields.lastname') }}
                    </th>
                    <td>
                        {{ $user->lastname }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.user.fields.email') }}
                    </th>
                    <td>
                        {{ $user->email }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('global.user.fields.email_verified_at') }}
                    </th>
                    <td>
                        {{ $user->email_verified_at }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Roles
                    </th>
                    <td>
                        @foreach($user->roles as $id => $roles)
                            <span class="label label-info label-many">{{ $roles->title }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Phone numbers</th>
                    <td>
                        @foreach($user->accountPhoneNumbers as $number)
                            / {{ $number->phone_number }} /
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Accounts</th>
                    <td>
                        @foreach($user->accounts as $number)
                            / {{ $number->name }} /
                        @endforeach
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@extends('layouts.admin')

@section('content')

    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-body">

                    <div class="mb-3">
                        <div class="btn btn-info send-mail-button">Send email to users</div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Notify</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Transaction count</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td style="width:70px; text-align:center;">
                                    <input type="checkbox" data-user-id="{{ $group->user->id }}" class="send-emails-to">
                                </td>
                                <td>{{ $group->user->name }}</td>
                                <td>{{ $group->user->email }}</td>
                                <td>{{ $group->transactions->count() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No users found.</td>
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

    <script>
      $(document).ready(function () {
        $('.send-mail-button').click(function () {

          let users = []
          $.each($('.send-emails-to:checked'), function (key, val) {
            users.push(parseInt($(val).attr('data-user-id')))
          })
          if (users.length > 0) {
            axios.post('/account/send-transaction-alerts', {users}).then(response => {
              alert('Emails sent.')
            })
          }

        })
      })
    </script>

@endsection
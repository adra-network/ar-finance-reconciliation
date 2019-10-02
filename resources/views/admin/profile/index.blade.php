@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.profile.save') }}" method="POST">
                        @csrf()

                        <h4>Notifications</h4>
                        <div class="form-group">
                            <input type="hidden" name="email_notifications_enabled" value="0">
                            <input type="checkbox" name="email_notifications_enabled" {{ $user->email_notifications_enabled ? 'checked' : null }} value="1">
                            <label>Enable email notifications</label>
                        </div>

                        <div>
                            <input type="submit" value="save" class="btn btn-success">
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Create comment
        </div>

        <div class="card-body">
            <form action="{{ route("account.comment-templates.update", $template->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" cols="30" rows="10">{{ $template->comment }}</textarea>
                </div>
                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>

@endsection
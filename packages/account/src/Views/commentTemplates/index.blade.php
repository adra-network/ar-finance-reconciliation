@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("account.comment-templates.create") }}">
                Add new comment
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Templates
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            Comment
                        </th>
                        <th>
                            &nbsp
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>
                                {{ $template->comment ?? '' }}
                            </td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('account.comment-templates.edit', $template->id) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

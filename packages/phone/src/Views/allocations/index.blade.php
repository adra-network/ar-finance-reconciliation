@extends('layouts.admin')
@section('content')
    @can('transaction_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("phone.allocations.create") }}">
                    {{ trans('global.add') }} {{ trans('global.allocations.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    
    <div class="card">
        <div class="card-header">
            {{ trans('global.allocations.title') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{ trans('global.allocations.name') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allocations as $allocation)
                        <tr>
                            <td>
                                {{ $allocation->name ?? '' }}
                            </td>
                            <td>
                                @can('transaction_edit')
                                    <a class="btn btn-xs btn-info"
                                       href="{{ route('phone.allocations.edit', $allocation->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('transaction_delete')
                                    <form action="{{ route('phone.allocations.destroy', $allocation->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"
                                               value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-header">Send PDF's</div>
                <div class="card-body">

                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endforeach
                            <hr>
                    @endif

                    <form action="{{ route('account.send-pdfs.send') }}" method="POST">
                        @csrf
                        <label>Import</label>
                        <select name="import_id" class="form-control">
                            <option value="null">Please select</option>
                            @foreach($imports as $import)
                                <option value="{{ $import->id }}">{{ $import->title }}</option>
                            @endforeach
                        </select>

                        <input type="submit" value="Send" class="btn btn-primary mt-3">

                        <hr>
                        <label>Accounts</label>
                        @foreach($accounts as $account)
                            <div>
                                <input type="checkbox" name="accounts[{{ $account->id }}]" value="1" checked> {{ $account->name }}
                            </div>
                        @endforeach

                        <hr>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

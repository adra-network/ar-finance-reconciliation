@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-header">Send PDFs</div>
                <div class="card-body">

                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endforeach
                        <hr>
                    @endif

                    <form action="{{ route('account.send-pdfs.send') }}" method="POST">
                        @csrf
                        <label>Statement Date* (ex. "<em>As of month/day/year</em>")</label>
                        <input name="statement_date" required type="text" class="form-control" value="As of {{ date('m/d/Y') }}">
                        <br />

                        <label>Accounts</label>
                        @foreach($accounts as $account)
                            <div>
                                <input type="checkbox" name="accounts[{{ $account->id }}]" value="1" checked> {{ $account->name }}
                            </div>
                        @endforeach

                        <br />
                        <input type="submit" value="Send" class="btn btn-primary mt-3">

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

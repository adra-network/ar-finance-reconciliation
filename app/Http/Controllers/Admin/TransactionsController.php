<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Transaction;

class TransactionsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        $transactions = Transaction::all();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('transaction_create'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.transactions.create', compact('accounts'));
    }

    public function store(StoreTransactionRequest $request)
    {
        abort_unless(\Gate::allows('transaction_create'), 403);

        $transaction = Transaction::create($request->all());

        return redirect()->route('admin.transactions.index');
    }

    public function edit(Transaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_edit'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $transaction->load('account');

        return view('admin.transactions.edit', compact('accounts', 'transaction'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_edit'), 403);

        $transaction->update($request->all());

        return redirect()->route('admin.transactions.index');
    }

    public function show(Transaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_show'), 403);

        $transaction->load('account');

        return view('admin.transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_delete'), 403);

        $transaction->delete();

        return back();
    }

    public function massDestroy(MassDestroyTransactionRequest $request)
    {
        Transaction::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }

    public function account()
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        return view('admin.transactions.account');
    }
}

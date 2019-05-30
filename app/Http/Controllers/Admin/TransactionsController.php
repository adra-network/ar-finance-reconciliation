<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Services\BatchTableService;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        $withPreviousMonths = $request->query('withPreviousMonths', 0);

        $batchTableService = new BatchTableService();
        $batchTableService->setWithPreviousMonths($withPreviousMonths);
        $batchTable = $batchTableService->getTableData();

        return view('admin.transactions.index', compact('batchTable'));
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

        $transaction = AccountTransaction::create($request->all());

        return redirect()->route('admin.transactions.index');
    }

    public function edit(AccountTransaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_edit'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $transaction->load('account');

        return view('admin.transactions.edit', compact('accounts', 'transaction'));
    }

    public function update(UpdateTransactionRequest $request, AccountTransaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_edit'), 403);

        $transaction->update($request->all());

        return redirect()->route('admin.transactions.index');
    }

    public function show(AccountTransaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_show'), 403);

        $transaction->load('account');

        return view('admin.transactions.show', compact('transaction'));
    }

    public function destroy(AccountTransaction $transaction)
    {
        abort_unless(\Gate::allows('transaction_delete'), 403);

        $transaction->delete();

        return back();
    }

    public function massDestroy(MassDestroyTransactionRequest $request)
    {
        AccountTransaction::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

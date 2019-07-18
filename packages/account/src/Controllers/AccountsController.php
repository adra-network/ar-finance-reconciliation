<?php

namespace Account\Controllers;

use Account\Models\Account;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Account\Requests\StoreAccountRequest;
use Account\Requests\UpdateAccountRequest;
use Account\Requests\MassDestroyAccountRequest;

class AccountsController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('account_access'), 403);

        $accounts = Account::all();

        return view('account::accounts.index', compact('accounts'));
    }

    public function create()
    {
        abort_unless(Gate::allows('account_create'), 403);

        return view('account::accounts.create');
    }

    public function store(StoreAccountRequest $request)
    {
        abort_unless(Gate::allows('account_create'), 403);

        $account = Account::create($request->all());

        return redirect()->route('account.accounts.index');
    }

    public function edit(Account $account)
    {
        abort_unless(Gate::allows('account_edit'), 403);

        return view('account::accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        abort_unless(Gate::allows('account_edit'), 403);

        $account->update($request->all());

        return redirect()->route('account.accounts.index');
    }

    public function show(Account $account)
    {
        abort_unless(Gate::allows('account_show'), 403);

        return view('account::accounts.show', compact('account'));
    }

    public function destroy(Account $account)
    {
        abort_unless(Gate::allows('account_delete'), 403);

        $account->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccountRequest $request)
    {
        Account::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

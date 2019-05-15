<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountMonth;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAccountMonthRequest;
use App\Http\Requests\StoreAccountMonthRequest;
use App\Http\Requests\UpdateAccountMonthRequest;

class AccountMonthController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('account_month_access'), 403);

        $accountMonths = AccountMonth::all();

        return view('admin.accountMonths.index', compact('accountMonths'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('account_month_create'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.accountMonths.create', compact('accounts'));
    }

    public function store(StoreAccountMonthRequest $request)
    {
        abort_unless(\Gate::allows('account_month_create'), 403);

        $accountMonth = AccountMonth::create($request->all());

        return redirect()->route('admin.account-months.index');
    }

    public function edit(AccountMonth $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_edit'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accountMonth->load('account');

        return view('admin.accountMonths.edit', compact('accounts', 'accountMonth'));
    }

    public function update(UpdateAccountMonthRequest $request, AccountMonth $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_edit'), 403);

        $accountMonth->update($request->all());

        return redirect()->route('admin.account-months.index');
    }

    public function show(AccountMonth $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_show'), 403);

        $accountMonth->load('account');

        return view('admin.accountMonths.show', compact('accountMonth'));
    }

    public function destroy(AccountMonth $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_delete'), 403);

        $accountMonth->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccountMonthRequest $request)
    {
        AccountMonth::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

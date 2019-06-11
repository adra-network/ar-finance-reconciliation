<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountMonth;
use App\AccountMonthlySummary;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAccountMonthRequest;
use App\Http\Requests\StoreAccountMonthRequest;
use App\Http\Requests\UpdateAccountMonthRequest;

class AccountMonthController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('account_month_access'), 403);

        $accountMonths = AccountMonthlySummary::all();

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

        $accountMonth = AccountMonthlySummary::create($request->all());

        return redirect()->route('admin.account-months.index');
    }

    public function edit(AccountMonthlySummary $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_edit'), 403);

        $accounts = Account::all()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accountMonth->load('account');

        return view('admin.accountMonths.edit', compact('accounts', 'accountMonth'));
    }

    public function update(UpdateAccountMonthRequest $request, AccountMonthlySummary $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_edit'), 403);

        $accountMonth->update($request->all());

        return redirect()->route('admin.account-months.index');
    }

    public function show(AccountMonthlySummary $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_show'), 403);

        $accountMonth->load('account');

        return view('admin.accountMonths.show', compact('accountMonth'));
    }

    public function destroy(AccountMonthlySummary $accountMonth)
    {
        abort_unless(\Gate::allows('account_month_delete'), 403);

        $accountMonth->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccountMonthRequest $request)
    {
        AccountMonthlySummary::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

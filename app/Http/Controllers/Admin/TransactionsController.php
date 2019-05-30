<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Repositories\AccountRepository;
use App\Repositories\AccountTransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        $withPreviousMonths = $request->query('withPreviousMonths', 0);
        $accounts = AccountRepository::getAccountsForTransactionsIndexPage($withPreviousMonths);
        $unallocatedTransactions = AccountTransactionRepository::getUnallocatedTransactionsWithoutGrouping();
        $transactionGroups = AccountTransactionRepository::getUnallocatedTransactionGroups();

        return view('admin.transactions.index', compact('accounts', 'unallocatedTransactions', 'transactionGroups'));
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

    public function account(Request $request)
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        $accounts = Account::all();

        $date = now();
        $months = [];
        $lastMonth = Carbon::parse('2017-01-01');
        do {
            $months[$date->format('m/Y')] = $date->format('Y-m');
            $date->subMonth();
        } while ($date->gte($lastMonth));

        $account_id = $request->input('account_id', false);
        $selectedMonth = $request->input('month', false);

        $transactions = null;
        $monthlySummary = null;
        if ($account_id && $selectedMonth) {
            $startDate = Carbon::parse($selectedMonth)->startOfMonth();
            $endDate = Carbon::parse($selectedMonth)->endOfMonth();

            $transactions = AccountTransaction::where('account_id', $account_id)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->get();

            $monthlySummary = AccountMonthlySummary::where('account_id', $account_id)
                ->whereYear('month_date', $startDate->year)
                ->whereMonth('month_date', $startDate->month)
                ->first();
        }

        return view('admin.transactions.account', compact('accounts', 'months', 'account_id', 'selectedMonth', 'transactions', 'monthlySummary'));
    }
}

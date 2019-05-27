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
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        $withPreviousMonths = $request->query('withPreviousMonths', 0);
        $accounts = AccountRepository::getAccountsForTransactionsIndexPage($withPreviousMonths);
        $unreconciledTransactions = AccountTransaction::whereNull('reconciliation_id')->get();

        return view('admin.transactions.index', compact('accounts', 'unreconciledTransactions'));
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
        $this_year = date("Y", strtotime("now"));
        $years[]=$this_year;
        while($this_year!=2017){
            $this_year--;
            $years[]=$this_year;
        }
        foreach($years as $year){
            for($month=12; $month>=1; $month--){
                if($month<10) $month="0".$month;
                $years_months[$month."/".$year]=$year."-".$month;
            }
        }
        $selected_account_id = $request->get('account_id', 0);
        $selected_month = $request->get('month', "");
        $transactions = NULL;
        $monthly_summaries = NULL;
        if($selected_account_id!=0 && $selected_month!="") {
            $serach_start_date=date("Y-m-01", strtotime($selected_month));
            $serach_end_date=date("Y-m-t", strtotime($selected_month));
            $transactions = \DB::table('account_transactions')
                ->select('account_transactions.*')
                ->where('account_transactions.account_id', $selected_account_id)
                ->where('account_transactions.transaction_date', '>=', $serach_start_date)
                ->where('account_transactions.transaction_date', '<=', $serach_end_date)
                ->get();
            $monthly_summaries = \DB::table('account_monthly_summaries')
                ->select('account_monthly_summaries.*')
                ->where('account_monthly_summaries.account_id', $selected_account_id)
                ->where('account_monthly_summaries.month_date', '>=', $serach_start_date)
                ->where('account_monthly_summaries.month_date', '<=', $serach_end_date)
                ->get();
        }
        return view('admin.transactions.account', compact('accounts', 'years_months', 'selected_account_id', 'selected_month', 'transactions', 'monthly_summaries'));
    }
}

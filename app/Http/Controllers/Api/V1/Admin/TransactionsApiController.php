<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\AccountTransaction;

class TransactionsApiController extends Controller
{
    public function index()
    {
        $transactions = AccountTransaction::all();

        return $transactions;
    }

    public function store(StoreTransactionRequest $request)
    {
        return AccountTransaction::create($request->all());
    }

    public function update(UpdateTransactionRequest $request, AccountTransaction $transaction)
    {
        return $transaction->update($request->all());
    }

    public function show(AccountTransaction $transaction)
    {
        return $transaction;
    }

    public function destroy(AccountTransaction $transaction)
    {
        return $transaction->delete();
    }
}

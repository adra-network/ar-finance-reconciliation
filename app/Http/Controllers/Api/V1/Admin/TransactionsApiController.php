<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Transaction;

class TransactionsApiController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();

        return $transactions;
    }

    public function store(StoreTransactionRequest $request)
    {
        return Transaction::create($request->all());
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        return $transaction->update($request->all());
    }

    public function show(Transaction $transaction)
    {
        return $transaction;
    }

    public function destroy(Transaction $transaction)
    {
        return $transaction->delete();
    }
}

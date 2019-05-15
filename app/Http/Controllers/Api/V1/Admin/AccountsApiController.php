<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;

class AccountsApiController extends Controller
{
    public function index()
    {
        $accounts = Account::all();

        return $accounts;
    }

    public function store(StoreAccountRequest $request)
    {
        return Account::create($request->all());
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        return $account->update($request->all());
    }

    public function show(Account $account)
    {
        return $account;
    }

    public function destroy(Account $account)
    {
        return $account->delete();
    }
}

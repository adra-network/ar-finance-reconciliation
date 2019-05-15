<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\AccountMonth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountMonthRequest;
use App\Http\Requests\UpdateAccountMonthRequest;

class AccountMonthApiController extends Controller
{
    public function index()
    {
        $accountMonths = AccountMonth::all();

        return $accountMonths;
    }

    public function store(StoreAccountMonthRequest $request)
    {
        return AccountMonth::create($request->all());
    }

    public function update(UpdateAccountMonthRequest $request, AccountMonth $accountMonth)
    {
        return $accountMonth->update($request->all());
    }

    public function show(AccountMonth $accountMonth)
    {
        return $accountMonth;
    }

    public function destroy(AccountMonth $accountMonth)
    {
        return $accountMonth->delete();
    }
}

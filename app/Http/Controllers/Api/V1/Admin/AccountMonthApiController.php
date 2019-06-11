<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\AccountMonthlySummary;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountMonthRequest;
use App\Http\Requests\UpdateAccountMonthRequest;

class AccountMonthApiController extends Controller
{
    public function index()
    {
        $accountMonths = AccountMonthlySummary::all();

        return $accountMonths;
    }

    public function store(StoreAccountMonthRequest $request)
    {
        return AccountMonthlySummary::create($request->all());
    }

    public function update(UpdateAccountMonthRequest $request, AccountMonthlySummary $accountMonth)
    {
        return $accountMonth->update($request->all());
    }

    public function show(AccountMonthlySummary $accountMonth)
    {
        return $accountMonth;
    }

    public function destroy(AccountMonthlySummary $accountMonth)
    {
        return $accountMonth->delete();
    }
}

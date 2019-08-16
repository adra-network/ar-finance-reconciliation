<?php

namespace Phone\Controllers;

use App\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Phone\DTO\TransactionListParameters;
use Phone\Repositories\TransactionListRepository;
use Phone\Repositories\AccountPhoneNumberRepository;

class TransactionsController extends Controller
{
    /**
     * @param TransactionListRepository $transactionListRepository
     * @param AccountPhoneNumberRepository $accountPhoneNumberRepository
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function index(
        TransactionListRepository $transactionListRepository,
        AccountPhoneNumberRepository $accountPhoneNumberRepository,
        Request $request
    ): View {
        $dateString = $request->input('dateFilter', null);
        $dates = $dateString ? explode(' - ', $dateString) : null;

        $params = new TransactionListParameters([
            'orderBy' => $request->input('orderDy', null),
            'orderDirection' => $request->input('orderDirection', TransactionListParameters::ORDER_BY_DESC),
            'dateFilter' => $dates,
            'numberFilter' => $request->input('numberFilter', null),
            'groupBy' => $request->input('groupBy', TransactionListParameters::GROUP_BY_NUMBER),
            'limit' => $request->input('limit', 100),
            'page' => $request->input('page', 1),
            'showZeroCharges' => $request->input('showZeroCharges', false),
        ]);

        $transactionListRepository->setParams($params);
        $transactionListRepository->setUser($request->user());
        $groups = $transactionListRepository->getTransactionListGroups();

        /** @var User $user */
        $user = auth()->user();
        $numbers = $accountPhoneNumberRepository->getNumbersForUser($user);

        return view('phone::transactions.index', [
            'groups' => $groups,
            'params' => $params,
            'numbers' => $numbers,
        ]);
    }
}

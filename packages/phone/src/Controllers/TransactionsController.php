<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Phone\Models\PhoneNumber;
use App\Http\Controllers\Controller;
use Phone\DTO\TransactionListParameters;
use Phone\Repositories\TransactionListRepository;

class TransactionsController extends Controller
{
    /**
     * @param TransactionListRepository $repository
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function index(TransactionListRepository $repository, Request $request): View
    {
        $dateString = $request->input('dateFilter', null);
        $dates = $dateString ? explode(' - ', $dateString) : null;

        $params = new TransactionListParameters([
            'orderBy'        => $request->input('orderDy', null),
            'orderDirection' => $request->input('orderDirection', TransactionListParameters::ORDER_BY_DESC),
            'dateFilter'     => $dates,
            'numberFilter'   => $request->input('numberFilter', null),
            'groupBy'        => $request->input('groupBy', TransactionListParameters::GROUP_BY_NUMBER),
            'limit'          => $request->input('limit', 100),
            'page'           => $request->input('page', 1),
            'showZeroCharges' => $request->input('showZeroCharges', false),
        ]);

        $repository->setParams($params);
        $groups = $repository->getTransactionListGroups();

        $numbers = PhoneNumber::all();

        return view('phone::transactions.index', [
            'groups'  => $groups,
            'params'  => $params,
            'numbers' => $numbers,
        ]);
    }
}

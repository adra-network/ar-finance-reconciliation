<?php

namespace Card\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TransactionsController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('card::transactions.index');
    }
}

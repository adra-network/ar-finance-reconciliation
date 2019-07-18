<?php

namespace Card\Controllers;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

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

<?php

//

use Illuminate\Support\Facades\Route;
use Phone\Controllers\ImportController;
use Phone\Controllers\PhoneNumbersController;
use Phone\Controllers\TransactionsController;

Route::group([
    'prefix'     => 'phone',
    'as'         => 'phone.',
    'middleware' => ['web', 'auth'],
], function () {
    Route::resource('transactions', TransactionsController::class, ['only' => ['index']]);

    Route::resource('import', ImportController::class)->only(['create', 'store']);

    Route::resource('phone-numbers', PhoneNumbersController::class, ['only' => ['index', 'edit', 'update']]);
});

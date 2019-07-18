<?php

//

use Illuminate\Support\Facades\Route;
use Card\Controllers\TransactionsController;

Route::group([
    'prefix'     => 'card',
    'as'         => 'card.',
    'middleware' => ['web', 'auth'],
], function () {
    Route::resource('transactions', TransactionsController::class, ['only' => ['index']]);
});

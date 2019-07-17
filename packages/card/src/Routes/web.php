<?php

//

use Card\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'card',
    'as'         => 'card.',
    'middleware' => ['web', 'auth'],
], function () {
    Route::resource('transactions', TransactionsController::class, ['only' => ['index']]);
});

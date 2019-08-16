<?php

//

use Illuminate\Support\Facades\Route;
use Phone\Controllers\ImportController;
use Phone\Controllers\AllocationsController;
use Phone\Controllers\TransactionsController;
use Phone\Controllers\CallerNumbersController;
use Phone\Controllers\PhoneTransactionModalController;

Route::group([
    'prefix'     => 'phone',
    'as'         => 'phone.',
    'middleware' => ['web', 'auth'],
], function () {
    Route::resource('transactions', TransactionsController::class, ['only' => ['index']]);

    Route::resource('import', ImportController::class)->only(['create', 'store']);

    Route::resource('caller-numbers', CallerNumbersController::class, ['only' => ['index', 'edit', 'update']]);

    Route::resource('allocations', AllocationsController::class);

    Route::post('transaction-modal/load', [PhoneTransactionModalController::class, 'load'])->name('transaction-modal.load');
    Route::post('transaction-modal/save', [PhoneTransactionModalController::class, 'save'])->name('transaction-modal.save');
});

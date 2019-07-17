<?php

//

use Account\Controllers\AccountsController;
use Account\Controllers\ImportController;
use Account\Controllers\ReconciliationModalController;
use Account\Controllers\TransactionCommentModalController;
use Account\Controllers\TransactionsController;
use Account\Controllers\TransactionsSummaryController;
use Account\Controllers\TransactionsSummaryExportController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'account',
    'as'         => 'account.',
    'middleware' => ['web', 'auth'],
], function () {
    Route::get('/transactions/summary', TransactionsSummaryController::class)->name('transactions.summary');
    Route::get('/transactions/export', TransactionsSummaryExportController::class)->name('transactions.export');

    Route::resource('import', ImportController::class)->only(['create', 'store']);

    Route::resource('transactions', TransactionsController::class);

    Route::get('transaction-comment-modal/{transaction_id}', [TransactionCommentModalController::class, 'index'])->name('transaction.comment.modal.index');
    Route::post('transaction-comment-modal', [TransactionCommentModalController::class, 'update'])->name('transaction.comment.modal.update');

    Route::get('reconciliation-modal/info', [ReconciliationModalController::class, 'info']);
    Route::post('reconciliation-modal/reconcile', [ReconciliationModalController::class, 'reconcile']);

    Route::delete('accounts/destroy', [AccountsController::class, 'massDestroy'])->name('accounts.massDestroy');
    Route::resource('accounts', AccountsController::class);
});

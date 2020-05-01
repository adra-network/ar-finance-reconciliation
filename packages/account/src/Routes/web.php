<?php

//

use Account\Controllers\CommentsController;
use Account\Controllers\CommentTemplateController;
use Account\Controllers\PdfSendingController;
use Account\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
use Account\Controllers\ImportController;
use Account\Controllers\AccountsController;
use Account\Controllers\TransactionsController;
use Account\Controllers\LateTransactionsController;
use Account\Controllers\ReconciliationModalController;
use Account\Controllers\TransactionsSummaryController;
use Account\Controllers\TransactionCommentModalController;
use Account\Controllers\TransactionsSummaryExportController;

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
    Route::post('reconciliation-modal/comment', [ReconciliationModalController::class, 'comment']);
    Route::post('reconciliation-modal/comment/{comment_id}/change-scope', [ReconciliationModalController::class, 'changeCommentScope']);

    Route::delete('accounts/destroy', [AccountsController::class, 'massDestroy'])->name('accounts.massDestroy');
    Route::resource('accounts', AccountsController::class);

    Route::get('late-transactions', [LateTransactionsController::class, 'index'])->name('late-transactions.index');

    Route::post('send-transaction-alerts', [AccountsController::class, 'sendTransactionAlerts'])->name('accounts.send-transaction-alerts');

    Route::resource('comment-templates', CommentTemplateController::class);

    Route::get('reports/employee-summary', [ReportsController::class, 'employeeSummary'])->name("reports.employee-summary");
    Route::get('reports/summaries-out-of-sync', [ReportsController::class, 'summariesOutOfSync'])->name("reports.summaries-out-of-sync");

    Route::delete('comments/{comment_id}', [CommentsController::class, 'destroy']);

    Route::get('send-pdfs', [PdfSendingController::class, 'index'])->name('send-pdfs.index');
    Route::post('send-pdfs/send', [PdfSendingController::class, 'send'])->name('send-pdfs.send');

});

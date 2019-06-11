<?php

Route::redirect('/', '/login');

Route::redirect('/home', '/admin');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');

    Route::resource('users', 'UsersController');

    Route::delete('account-months/destroy', 'AccountMonthController@massDestroy')->name('account-months.massDestroy');

    Route::delete('accounts/destroy', 'AccountsController@massDestroy')->name('accounts.massDestroy');

    Route::resource('accounts', 'AccountsController');

    Route::resource('account-months', 'AccountMonthController');

    Route::delete('transactions/destroy', 'TransactionsController@massDestroy')->name('transactions.massDestroy');

    Route::resource('transactions', 'TransactionsController');
    Route::get('transaction-comment-modal/{transaction_id}', 'TransactionCommentModalController@index')->name('transaction.comment.modal.index');
    Route::post('transaction-comment-modal', 'TransactionCommentModalController@update')->name('transaction.comment.modal.update');

    Route::resource('import', 'ImportController')->only(['create', 'store']);

    Route::delete('audit-logs/destroy', 'AuditLogsController@massDestroy')->name('audit-logs.massDestroy');

    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    Route::get('reconciliation-modal/info', 'ReconciliationModalController@info');
    Route::post('reconciliation-modal/reconcile', 'ReconciliationModalController@reconcile');

    Route::get('search', 'SearchController@search')->name('search');

    Route::get('account/transactions', 'AccountTransactionsController')->name('account.transactions');
    Route::get('account/transactions/export', 'ExportController@accountTransactions')->name('account.transactions.export');
});

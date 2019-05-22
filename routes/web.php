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

    Route::get('transactions/account', 'TransactionsController@account')->name('transactions.account');
    Route::resource('transactions', 'TransactionsController');

    Route::resource('import', 'ImportController')->only(['create', 'store']);

    Route::delete('audit-logs/destroy', 'AuditLogsController@massDestroy')->name('audit-logs.massDestroy');

    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    Route::get('transaction-reconciliation/modal-info', 'TransactionReconciliationController@modalInfo');
    Route::post('transaction-reconciliation', 'TransactionReconciliationController@reconcileTransactions');
});

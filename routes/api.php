<?php

Route::group(['prefix' => 'v1', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');

    Route::apiResource('users', 'UsersApiController');

    Route::apiResource('accounts', 'AccountsApiController');

    Route::apiResource('account-months', 'AccountMonthApiController');

    Route::apiResource('transactions', 'TransactionsApiController');
});

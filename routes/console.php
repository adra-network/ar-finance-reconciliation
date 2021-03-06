<?php

use Account\Models\Reconciliation;
use Account\TransactionAlertSystem\EmailUsersAction;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('email-users', function () {
    (new EmailUsersAction())();
});

Artisan::command('recache-reconciliations', function () {
    Reconciliation::get()->each(function (Reconciliation $reconciliation) {
        $reconciliation->cacheIsFullyReconciledAttribute(true);
    });
});
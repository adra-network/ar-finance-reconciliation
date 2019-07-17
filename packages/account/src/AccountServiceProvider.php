<?php

namespace Account;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'account');
    }
}

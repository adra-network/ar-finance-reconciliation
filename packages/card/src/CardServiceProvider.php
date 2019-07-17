<?php

namespace Card;

use Illuminate\Support\ServiceProvider;

class CardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'card');
    }
}

<?php

namespace Phone;

use Illuminate\Support\ServiceProvider;

class PhoneServiceProvider extends ServiceProvider
{
    public function boot():void
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'phone');
    }
}

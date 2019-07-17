<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Account\Models\Account;
use Account\Models\MonthlySummary;
use Faker\Generator as Faker;

$factory->define(MonthlySummary::class, function (Faker $faker) {
    return [
        'month_date'        => now()->format('Y-m-d'),
        'net_change'        => $faker->numberBetween(1, 100),
        'export_date'       => now(),
        'ending_balance'    => $faker->numberBetween(1, 100),
        'beginning_balance' => $faker->numberBetween(1, 100),
        'account_id'        => factory(Account::class),
    ];
});

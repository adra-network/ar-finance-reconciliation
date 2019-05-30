<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\AccountMonthlySummary;
use Faker\Generator as Faker;

$factory->define(AccountMonthlySummary::class, function (Faker $faker) {
    return [
        'month_date' => now()->format('Y-m-d'),
        'net_change' => $faker->numberBetween(1, 100),
        'export_date' => now(),
        'ending_balance' => $faker->numberBetween(1, 100),
        'beginning_balance' => $faker->numberBetween(1, 100),
    ];
});

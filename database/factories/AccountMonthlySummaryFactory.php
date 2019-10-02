<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Account\Models\Account;
use Faker\Generator as Faker;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;

$factory->define(MonthlySummary::class, function (Faker $faker) {
    return [
        'month_date' => now()->format('Y-m-d'),
        'net_change' => $faker->numberBetween(1, 100),
        'export_date' => now(),
        'ending_balance' => $faker->numberBetween(1, 100),
        'beginning_balance' => $faker->numberBetween(1, 100),
        'account_id' => factory(Account::class),
        'account_import_id' => factory(AccountImport::class),
        'date_from' => now(),
        'date_to' => now(),
    ];
});

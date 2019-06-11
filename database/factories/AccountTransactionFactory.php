<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Account;
use App\AccountTransaction;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(AccountTransaction::class, function (Faker $faker) {
    $debitIsZero = $faker->numberBetween(0, 1);

    return [
        'transaction_date' => $faker->dateTimeBetween(now()->startOfMonth(), now()->endOfMonth()),
        'code'             => Str::random(),
        'journal'          => Str::random(),
        'reference'        => Str::random(),
        'debit_amount'     => $debitIsZero ? 0 : $faker->randomElement([10, 20, 30, 40, 50]),
        'credit_amount'    => $debitIsZero ? $faker->randomElement([10, 20, 30, 40, 50]) : 0,
        'comment'          => $faker->text(30),
        'status'           => $faker->randomElement(AccountTransaction::STATUS_SELECT),
        'account_id'       => factory(Account::class),
    ];
});

<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Account\Models\Reconciliation;

$factory->define(Reconciliation::class, function (Faker $faker) {
    return [
        'is_fully_reconciled' => 0,
        'comment'             => $faker->text(100),
    ];
});

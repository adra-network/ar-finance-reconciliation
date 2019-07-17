<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Account\Models\Reconciliation;
use Faker\Generator as Faker;

$factory->define(Reconciliation::class, function (Faker $faker) {
    return [
        'is_fully_reconciled' => 0,
        'comment'             => $faker->text(100),
    ];
});

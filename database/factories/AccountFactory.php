<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Account\Models\Account;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'code'  => Str::random(),
        'name'  => $faker->name,
        'email' => $faker->email,
    ];
});

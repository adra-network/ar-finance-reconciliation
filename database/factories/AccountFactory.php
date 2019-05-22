<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Account;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'code' => Str::random(),
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

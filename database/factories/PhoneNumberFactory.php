<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Phone\Models\PhoneNumber;

$factory->define(PhoneNumber::class, function (Faker $faker) {
    return [
        'phone_number' => $faker->phoneNumber,
    ];
});

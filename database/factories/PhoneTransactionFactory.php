<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Phone\Models\PhoneNumber;
use Phone\Models\PhoneTransaction;

$factory->define(PhoneTransaction::class, function (Faker $faker) {
    return [
        'phone_number_id'       => factory(PhoneNumber::class),
        'date'                  => $faker->dateTimeBetween('-1 month', '+1 month'),
        'total_charges'         => $faker->numberBetween(1, 1000),
        'minutes_used'          => $faker->numberBetween(1, 100),
        'number_called_to_from' => $faker->numberBetween(1, 2),
    ];
});

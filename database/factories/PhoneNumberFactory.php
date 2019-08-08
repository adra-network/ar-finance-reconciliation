<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Phone\Models\Allocation;
use Faker\Generator as Faker;
use Phone\Models\PhoneNumber;

$factory->define(PhoneNumber::class, function (Faker $faker) {
    return [
        'phone_number' => $faker->phoneNumber,
        'allocation_id' => factory(Allocation::class),
    ];
});

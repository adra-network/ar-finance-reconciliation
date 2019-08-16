<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Phone\Models\Allocation;
use Faker\Generator as Faker;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;

$factory->define(PhoneTransaction::class, function (Faker $faker) {
    return [
        'caller_phone_number_id' => factory(CallerPhoneNumber::class),
        'account_phone_number_id' => factory(AccountPhoneNumber::class),
        'date' => $faker->dateTimeBetween('-1 month', '+1 month'),
        'total_charges' => $faker->numberBetween(0, 1) === 0 ? 0 : $faker->numberBetween(1, 1000),
        'minutes_used' => $faker->numberBetween(1, 100),
        'number_called_to_from' => $faker->numberBetween(1, 2),
        'allocation_id' => factory(Allocation::class),
    ];
});

<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use Faker\Generator as Faker;
use Phone\Models\AccountPhoneNumber;

$factory->define(AccountPhoneNumber::class, function (Faker $faker) {
    return [
        'phone_number' => $faker->phoneNumber,
        'user_id' => factory(User::class),
    ];
});

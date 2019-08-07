<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Phone\Enums\ChargeTo;
use Phone\Models\Allocation;
use Faker\Generator as Faker;

$factory->define(Allocation::class, function (Faker $faker) {
    $chargeTo = random_int(0, count(ChargeTo::ENUM) - 1);
    $chargeTo = ChargeTo::ENUM[$chargeTo];

    return [
        'name' => $faker->name,
        'charge_to' => $chargeTo,
        'account_number' => $chargeTo === ChargeTo::ACCOUNT ? 'testnumber' : null,
    ];
});

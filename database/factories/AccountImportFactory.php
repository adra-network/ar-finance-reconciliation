<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use Faker\Generator as Faker;
use Account\Models\AccountImport;

$factory->define(AccountImport::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'title' => $faker->title,
        'filename' => 'import-'.time().'.csv',
        'date_from' => now(),
        'date_to' => now(),
    ];
});

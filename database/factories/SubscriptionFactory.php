<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Subscription::class, function (Faker $faker) {
    return [
        'recnum' => $faker->randomNumber(7),
        'title' => $faker->sentence(3, true),
        'state_id' => 1,
        'user_id' => -1,
    ];
});

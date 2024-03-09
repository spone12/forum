<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory 
 */

use Faker\Generator as Faker;
use App\Models\Notation\NotationViewModel;

$factory->define(
    NotationViewModel::class, function (Faker $faker) {

        static $i = 0;
        return [
        'notation_id' => ++$i,
        'counter_views' => rand(100, 500),
        'view_date' => $faker->dateTimeBetween('-9 months', '-1 days')
        ];
    }
);

$factory->state(NotationViewModel::class, 'now', ['view_date' => now()]);

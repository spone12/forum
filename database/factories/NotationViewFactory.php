<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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
$autoIncrement = autoIncrement();

$factory->defineAs(App\Http\Model\Notation\NotationViewModel::class, 'views_notation', 
    function(Faker $faker) use ($autoIncrement)
{
    $autoIncrement->next();
    $this_value = (int) $autoIncrement->current();

    return [
        //$faker->numberBetween(199,499);
        'notation_id' => $this_value,
        'counter_views' => rand(100,500),
        'view_date' => $faker->dateTimeBetween('-9 months', '-1 days')
    ];
});

function autoIncrement()
{
    for ($i = 0; $i < 71; $i++) 
    {
        yield $i;
    }
}
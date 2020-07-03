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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('12345678'),
        'remember_token' => Str::random(10),
        'ip_user' => rand(1, 254).'.'.rand(1, 254).'.'.rand(1, 254).'.'.rand(1, 254),
        'browser_user' => Str::random(10)
        //'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});

$factory->defineAs(App\Http\Model\NotationModel::class, 'notation', function(Faker $faker){
    $faker = \Faker\Factory::create('ru_RU');

    return [
        'id_user' => \App\User::query()->inRandomOrder()->first()->id,
        'name_notation' => Str::random(10),
        'text_notation' => $faker->realText(rand(100,500)),
        'notation_add_date' => $faker->dateTimeBetween('-9 months', '-1 days')
    ];
});
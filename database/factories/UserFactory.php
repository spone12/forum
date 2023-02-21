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
        'email_verified_at' =>  $faker->dateTimeBetween('-9 months', '-1 days'),
        'password' => bcrypt('12345678'),
        'last_online_at' =>  $faker->dateTimeBetween('-12 months', '-1 days'),
        'gender' => rand(1,2),
        'remember_token' => Str::random(10),
        'ip_user' => $faker->ipv4(),
        'browser_user' =>  \Faker\Provider\UserAgent::userAgent()
    ];
});

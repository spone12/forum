<?php

namespace Database\Factories;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Notation
     *
     * @return array
     */
    public function definition()
    {
        $this->faker = \Faker\Factory::create('ru_RU');

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' =>  $this->faker->dateTimeBetween('-9 months', '-1 days'),
            'password' => bcrypt('12345678'),
            'last_online_at' =>  $this->faker->dateTimeBetween('-12 months', '-1 days'),
            'gender' => rand(1, 2),
            'remember_token' => Str::random(10),
            'registration_ip' => $this->faker->ipv4(),
            'user_agent' =>  \Faker\Provider\UserAgent::userAgent()
        ];
    }
}

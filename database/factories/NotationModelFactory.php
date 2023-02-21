<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\NotationModel;

class NotationModelFactory extends Factory
{
    protected $model = NotationModel::class;

    /**
     * Notation
     *
     * @return array
     */
    public function definition()
    {
        $this->faker = \Faker\Factory::create('ru_RU');

        return [
            'id_user' => \App\User::query()->inRandomOrder()->first()->id,
            'name_notation' => Str::random(10),
            'text_notation' => $this->faker->realText(rand(100,500)),
            'notation_add_date' => $this->faker->dateTimeBetween('-9 months', '-1 days')
        ];
    }
}

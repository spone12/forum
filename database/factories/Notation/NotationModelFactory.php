<?php

namespace Database\Factories\Notation;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notation\NotationModel;

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
            'user_id' => \App\User::query()->inRandomOrder()->first()->id,
            'name_notation' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'text_notation' => $this->faker->realText(
                $this->faker->numberBetween(100,500)
            ),
            'notation_add_date' => $this->faker->dateTimeBetween('-12 months', 'now')
        ];
    }
}

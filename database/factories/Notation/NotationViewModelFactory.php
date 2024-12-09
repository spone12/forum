<?php

namespace Database\Factories\Notation;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notation\NotationViewModel;

class NotationViewModelFactory extends Factory
{
    protected $model = NotationViewModel::class;

    /**
     * Notation
     *
     * @return array
     */
    public function definition()
    {
        $this->faker = \Faker\Factory::create('ru_RU');

        static $i = 0;
        return [
            'notation_id' => ++$i,
            'counter_views' => rand(100, 500),
            'view_date' => now()
        ];
    }

    /**
     * View random date state
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function viewRandomDateState(string $startDate = '-12 months', string $endDate = 'now')
    {
        return $this->state(function ($startDate) {
            return [
                'view_date' => $this->faker->dateTimeBetween('-12 months', 'now')
            ];
        });
    }
}

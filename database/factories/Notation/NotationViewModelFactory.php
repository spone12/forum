<?php

namespace Database\Factories\Notation;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notation\{NotationViewModel, NotationModel};


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

        return [
            'notation_id' => NotationModel::factory(),
            'counter_views' => $this->faker->numberBetween(100,500),
            'view_date' => now()
        ];
    }

    /**
     * View random date state
     *
     * @param string $startDate
     * @param string $endDate
     * @return NotationViewModelFactory
     */
    public function viewRandomDateState(string $startDate = '-12 months', string $endDate = 'now')
    {
        return $this->state(function ($attributes) use ($startDate, $endDate) {
            return [
                'view_date' => $this->faker->dateTimeBetween($startDate,  $endDate)
            ];
        });
    }
}

<?php

namespace Database\Factories\Chat;

use App\Models\Chat\{
    MessagesModel,
    DialogModel
};
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat\MessagesModel>
 */
class MessagesModelFactory extends Factory
{
    protected $model = MessagesModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dialog_id' => DialogModel::factory(),
            'user_id' => User::factory(),
            'text' => $this->faker->realText(50),
            'read' => 0
        ];
    }
}

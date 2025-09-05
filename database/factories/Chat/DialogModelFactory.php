<?php

namespace Database\Factories\Chat;

use App\Enums\Chat\DialogType;
use App\Models\Chat\{DialogModel, DialogParticipants};
use Illuminate\Database\Eloquent\Factories\Factory;
use App\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DialogModelFactory extends Factory
{
    protected $model = DialogModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'type' => DialogType::PRIVATE,
            'created_by' => User::factory()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (DialogModel $dialog)
        {
            DialogParticipants::factory()->owner($dialog->created_by)->create([
                'dialog_id' => $dialog->dialog_id,
            ]);

            // For a private dialogue, we add another user
            if ($dialog->type === DialogType::PRIVATE) {
                DialogParticipants::factory()->create([
                    'dialog_id' => $dialog->dialog_id,
                ]);
            }
        });
    }
}

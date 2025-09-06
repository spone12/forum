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

    /**
     * The participant of the dialogue the creator must always be
     *
     * @return DialogModelFactory|Factory
     */
    public function configure()
    {
        return $this->afterCreating(function (DialogModel $dialog)
        {
            DialogParticipants::factory()->owner($dialog->created_by)->create([
                'dialog_id' => $dialog->dialog_id,
            ]);
        });
    }

    /**
     * Private chat
     *
     * @return DialogModelFactory|Factory
     */
    public function private()
    {
        return $this->state(['type' => DialogType::PRIVATE])
            ->has(
                DialogParticipants::factory()->count(1),
                'participants'
            );
    }

    /**
     * Group chat
     *
     * @param int $count
     * @return DialogModelFactory|Factory
     */
    public function group(int $count = 2)
    {
        return $this->state(['type' => DialogType::GROUP])
            ->has(
                DialogParticipants::factory()->count($count),
                'participants'
            );
    }
}

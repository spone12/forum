<?php

namespace Database\Factories\Chat;

use App\Enums\Chat\DialogType;
use App\Models\Chat\{DialogModel, DialogParticipants, MessagesModel};
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
     * Set private chat
     *
     * @return DialogModelFactory|Factory
     */
    public function private()
    {
        return $this->state(['type' => DialogType::PRIVATE]);
    }

    /**
     * Set group chat
     *
     * @param int $count
     * @return DialogModelFactory|Factory
     */
    public function group(int $count = 2)
    {
        return $this->state([
            'type' => DialogType::GROUP,
            'title' => $this->faker->sentence(3)
        ])
        ->has(
            DialogParticipants::factory()->count($count),
            'participants'
        );
    }

    /**
     * Create messages from a specific users
     *
     * @param array $users
     * @param int $count
     * @return DialogModelFactory|Factory
     */
    public function withMessagesFrom(array $users, int $count = 1)
    {
        return $this->afterCreating(function (DialogModel $dialog) use ($users, $count) {
            foreach ($users as $user) {
                MessagesModel::factory()
                    ->count($count)
                    ->for($dialog, 'dialog')
                    ->for($user, 'user')
                    ->create();
            }
        });
    }

    /**
     * Add users to the dialog
     *
     * @param array $users
     * @return DialogModelFactory|Factory
     */
    public function addUsersToDialog(array $users)
    {
        return $this->afterCreating(function (DialogModel $dialog) use ($users) {
            foreach ($users as $user) {
                DialogParticipants::factory()
                    ->for($dialog, 'dialog')
                    ->for($user, 'user')
                    ->create();
            }
        });
    }
}

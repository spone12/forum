<?php

namespace Database\Factories\Chat;

use App\Models\Chat\DialogModel;
use App\Models\Chat\DialogParticipants;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Chat\ChatRole;
use App\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DialogParticipantsFactory extends Factory
{
    protected $model = DialogParticipants::class;

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
            'role' => ChatRole::MEMBER->value,
        ];
    }

    /**
     * Status: member-OWNER
     *
     * @param int $userId
     * @return DialogParticipantsFactory|Factory
     */
    public function owner(int $userId)
    {
        return $this->state(fn () => [
            'user_id' => $userId,
            'role' => ChatRole::OWNER->value,
        ]);
    }
}

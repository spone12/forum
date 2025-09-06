<?php

namespace Tests\Feature\Chat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Chat\DialogModel;
use Tests\TestCase;
use App\Enums\Chat\ChatRole;

class DialogFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_dialog_creates_with_participants(): void
    {
        $dialog = DialogModel::factory()->create();

        $this->assertDatabaseHas('dialogs', [
            'dialog_id' => $dialog->dialog_id,
        ]);

        $this->assertDatabaseHas('dialog_participants', [
            'dialog_id' => $dialog->dialog_id,
            'user_id' => $dialog->created_by,
            'role' => ChatRole::OWNER->value,
        ]);
    }
}

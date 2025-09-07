<?php

namespace Tests\Feature\Chat\Dialog;

use App\Enums\ResponseCodeEnum;
use App\Models\Chat\{DialogModel, MessagesModel};
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DialogTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var User $user */
    protected User $user;
    /** @var DialogModel $dialog */
    protected DialogModel $dialog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->dialog = DialogModel::factory()
            ->private()
            ->for($this->user, 'createdBy')
            ->withMessagesFrom($this->user)
            ->create();
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::dialogList
     * @return void
     */
    public function test_user_can_see_his_dialogs()
    {
        $this->actingAs($this->user);
        $this->assertDatabaseHas('dialogs', [
            'dialog_id' => $this->dialog->dialog_id,
        ]);

        $response = $this->get(route('dialogList'));
        $response->assertStatus(ResponseCodeEnum::OK);

        $response->assertViewIs('menu.Chat.chat');

        $response->assertViewHas('dialogList', function ($dialogList) {
            return $dialogList->contains('dialog_id', $this->dialog->dialog_id);
        });
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::dialogList
     * @return void
     */
    public function test_user_cannot_see_foreign_dialogs()
    {
        $this->actingAs($this->user);

        // Create another user
        $anotherUser = User::factory()->create();

        // Create another dialog
        $foreignDialog = DialogModel::factory()
            ->withMessagesFrom($anotherUser)
            ->create([
                'created_by' => $anotherUser->id,
            ]);

        $response = $this->get(route('dialogList'));

        $response->assertStatus(ResponseCodeEnum::OK);
        $response->assertViewIs('menu.Chat.chat');

        // The authorized user should not see other dialogs
        $response->assertViewHas('dialogList', function ($dialogList) use ($foreignDialog) {
            return !$dialogList->contains('dialog_id', $foreignDialog->dialog_id);
        });
    }
}

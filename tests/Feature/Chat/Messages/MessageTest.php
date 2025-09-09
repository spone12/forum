<?php

namespace Chat\Messages;

use App\Enums\ResponseCodeEnum;
use App\User;
use App\Models\Chat\DialogModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use App\Events\ChatMessageEvent;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var User $user */
    protected User $user;
    /** @var User $anotherUser */
    protected User $anotherUser;
    /** @var DialogModel $dialog */
    protected DialogModel $dialog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->dialog = DialogModel::factory()
            ->private()
            ->for($this->user, 'createdBy')
            ->addUsersToDialog([$this->anotherUser])
            ->withMessagesFrom([$this->user])
            ->create();
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::send
     * @return void
     */
    public function test_user_send_message_success()
    {
        Event::fake();

        // Authorization
        $this->actingAs($this->user);

        // Data to send
        $payload = [
            'dialogId' => $this->dialog->dialog_id,
            'message' => $this->faker->realText(50),
        ];

        // Query
        $response = $this->postJson(route('sendMessage'), $payload);

        // Checking for a successful response
        $response->assertStatus(ResponseCodeEnum::CREATED);

        // We check that the message was actually created
        $this->assertDatabaseHas('messages', [
            'dialog_id' => $this->dialog->dialog_id,
            'user_id' => $this->user->id,
            'text' => $payload['message'],
        ]);

        // Checking the structure of the response
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'created_at',
            ],
            'message'
        ]);

        Event::assertDispatched(ChatMessageEvent::class);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::send
     * @return void
     */
    public function test_user_cannot_send_message_to_foreign_dialog()
    {
        Event::fake();

        $anotherUser = User::factory()->create();
        $this->actingAs($anotherUser);

        $response = $this->postJson(route('sendMessage'), [
            'dialogId' => $this->dialog->dialog_id,
            'message' => $this->faker->realText(50),
        ]);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::edit
     * @return void
     */
    public function test_user_can_edit_his_message()
    {
        $this->actingAs($this->user);

        $messageId = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first()->message_id;
        $newMessageText = $this->faker->realText(50);

        $response = $this->putJson(route('editMessage'), [
            'dialogId' => $this->dialog->dialog_id,
            'message' => $newMessageText,
            'messageId' => $messageId
        ]);

        $response->assertStatus(ResponseCodeEnum::OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'updated_at',
            ],
            'message'
        ]);

        $this->assertDatabaseHas('messages', [
            'message_id' => $messageId,
            'text'       => $newMessageText,
        ]);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::edit
     * @return void
     */
    public function test_user_cannot_edit_foreign_message()
    {
        // Create another user
        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser);

        // Take the message of the current user
        $messageId = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first()->message_id;

        $newMessageText = 'Forbidden edit!';

        // Try to edit foreign message
        $response = $this->putJson(route('editMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'message'   => $newMessageText,
            'messageId' => $messageId,
        ]);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN);

        // Проверяем, что текст не изменился
        $this->assertDatabaseMissing('messages', [
            'message_id' => $messageId,
            'text'       => $newMessageText,
        ]);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::edit
     * @return void
     */
    public function test_edit_nonexistent_message_returns_not_found()
    {
        $this->actingAs($this->user);

        // The message is guaranteed not exists
        $nonExistentMessageId = 999999;

        $response = $this->putJson(route('editMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'message'   => 'New text',
            'messageId' => $nonExistentMessageId,
        ]);

        $response->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::delete
     * @return void
     */
    public function test_user_can_delete_his_message()
    {
        $this->actingAs($this->user);
        $messageId = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first()->message_id;

        $response = $this->deleteJson(route('deleteMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $messageId,
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'deleted_at',
            ],
            'message'
        ]);

        $this->assertSoftDeleted('messages', [
            'message_id' => $messageId,
        ]);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::delete
     * @return void
     */
    public function test_user_cannot_delete_foreign_message()
    {
        // Create another user
        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser);

        // Take the message of the current user
        $messageId = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first()->message_id;

        // Try to delete foreign message
        $response = $this->deleteJson(route('deleteMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $messageId,
        ]);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::edit
     * @return void
     */
    public function test_delete_nonexistent_message_returns_not_found()
    {
        $this->actingAs($this->user);

        // The message is guaranteed not exists
        $nonExistentMessageId = 999999;

        $response = $this->deleteJson(route('deleteMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $nonExistentMessageId,
        ]);

        $response->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::recover
     * @return void
     */
    public function test_user_can_recover_his_message()
    {
        $this->actingAs($this->user);
        $message = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first();

        // Soft delete
        $message->delete();

        $this->assertSoftDeleted('messages', [
            'message_id' => $message->message_id
        ]);

        $response = $this->putJson(route('recoverMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $message->message_id
        ]);

        $response->assertStatus(ResponseCodeEnum::OK);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'updated_at',
            ],
            'message'
        ]);

        $this->assertDatabaseHas('messages', [
            'message_id' => $message->message_id,
            'deleted_at' => null
        ]);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::recover
     * @return void
     */
    public function test_user_cannot_recover_foreign_message()
    {
        // Create another user
        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser);

        // Take the message of the current user
        $message = $this->dialog->messages
            ->where('user_id', $this->user->id)
            ->first();

        // Soft delete
        $message->delete();

        $this->assertSoftDeleted('messages', [
            'message_id' => $message->message_id
        ]);

        $response = $this->putJson(route('recoverMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $message->message_id
        ]);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\Messages\MessageController::recover
     * @return void
     */
    public function test_recover_nonexistent_message_returns_not_found()
    {
        $this->actingAs($this->user);

        // The message is guaranteed not exists
        $nonExistentMessageId = 999999;

        $response = $this->putJson(route('recoverMessage'), [
            'dialogId'  => $this->dialog->dialog_id,
            'messageId' => $nonExistentMessageId
        ]);
        $response->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }
}

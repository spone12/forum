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

class SendMessageTest extends TestCase
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
            ->for($this->user, 'createdBy')
            ->private()
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
}

<?php

namespace Tests\Feature\Chat;

use App\Enums\ResponseCodeEnum;
use App\Models\Chat\DialogModel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
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
     * @covers \App\Http\Controllers\Chat\ChatSearchController::searchAll
     * @return void
     */
    public function test_user_search_messages_successfully()
    {
        $this->actingAs($this->user);

        $searchText = $this->dialog->messages->first()->text;

        $response = $this->getJson(route('searchAllChat', [
            'searchText' => $searchText
        ]));

        $response->assertStatus(ResponseCodeEnum::OK);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'searchResultMessage',
                'items' => [
                    '*' => ['id', 'name', 'avatar', 'dialog_id', 'created_at', 'text']
                ]
            ],
            'message'
        ]);

        $results = collect($response->json('data')['items']);
        $this->assertTrue(
            $results->contains(fn($msg) => str_contains($msg['text'], substr($searchText, 0, 10)))
        );
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\ChatSearchController::searchAll
     * @return void
     */
    public function test_search_returns_empty_for_nonexistent_text()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(route('searchAllChat', ['searchText' => 'text_not_in_db']));

        $response->assertStatus(ResponseCodeEnum::OK);
        $this->assertEmpty($response->json('data')['items']);
    }

    /**
     *
     * @covers \App\Http\Controllers\Chat\ChatSearchController::searchAll
     * @return void
     */
    public function test_search_requires_authentication()
    {
        $response = $this->getJson(route('searchAllChat', ['searchText' => 'text']));
        $response->assertStatus(ResponseCodeEnum::UNAUTHORIZED);
    }
}

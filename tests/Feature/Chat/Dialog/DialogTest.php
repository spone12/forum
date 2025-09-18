<?php

namespace Tests\Feature\Chat\Dialog;

use App\Enums\ResponseCodeEnum;
use App\Models\Chat\DialogModel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DialogTest extends TestCase
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
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::dialogList
     * @return void
     */
    public function testUserCanSeeHisDialogs()
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
    public function testUserCannotSeeForeignDialogs()
    {
        $this->actingAs($this->user);

        // Create another user
        $anotherUser = User::factory()->create();
        $anotherUser2 = User::factory()->create();

        // Create another dialog
        $foreignDialog = DialogModel::factory()
            ->for($anotherUser, 'createdBy')
            ->addUsersToDialog([$anotherUser2])
            ->withMessagesFrom([$anotherUser])
            ->create();

        $response = $this->get(route('dialogList'));

        $response->assertStatus(ResponseCodeEnum::OK);
        $response->assertViewIs('menu.Chat.chat');

        // The authorized user should not see other dialogs
        $response->assertViewHas('dialogList', function ($dialogList) use ($foreignDialog) {
            return !$dialogList->contains('dialog_id', $foreignDialog->dialog_id);
        });
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::open
     * @return void
     */
    public function testOpenExistingDialogReturnsIt()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('openDialog', ['userId' => $this->anotherUser->id]));
        $response->assertStatus(ResponseCodeEnum::FOUND);
        $response->assertRedirect(route('dialog', ['dialogId' => $this->dialog->dialog_id]));
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::open
     * @return void
     */
    public function testOpenNewDialogCreatesAndReturnsIt()
    {
        $this->actingAs($this->user);
        $anotherUser = User::factory()->create();

        $response = $this->get(route('openDialog', ['userId' => $anotherUser->id]));
        $response->assertStatus(ResponseCodeEnum::FOUND);

        // Getting the created dialog number
        $redirectUrl = $response->headers->get('Location');
        $dialogId = basename(parse_url($redirectUrl, PHP_URL_PATH));
        $response->assertRedirect(route('dialog', ['dialogId' => $dialogId]));
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::open
     * @return void
     */
    public function testOpenDialogWithNonexistentUserFails()
    {
        $this->actingAs($this->user);

        $response = $this->get(
            route('openDialog', [
                'userId' => User::max('id') + 1
            ])
        );
        $response->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::getDialogMessages
     * @return void
     */
    public function testUserCanGetMessagesFromDialog()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dialog', ['dialogId' => $this->dialog->dialog_id]));
        $response->assertStatus(ResponseCodeEnum::OK);

        $response->assertViewHas('dialogObj');
        $response->assertViewHas('dialogId', $this->dialog->dialog_id);

        // Check if the current conversation is present in recent dialogs
        $dialogId = $this->dialog->dialog_id;
        $response->assertViewHas('lastDialogs', function ($lastDialogs) use ($dialogId) {
            return $lastDialogs->contains('dialog_id', $dialogId);
        });

        $messages = $response->viewData('dialogObj');
        $this->assertGreaterThan(0, $messages->count());
        $this->assertEquals($this->user->id, $messages->first()->user_id);
    }

    /**
     * @covers \App\Http\Controllers\Chat\Dialog\DialogController::getDialogMessages
     * @return void
     */
    public function testUserCannotGetMessagesFromForeignDialog()
    {
        $this->actingAs($this->user);

        $anotherUser = User::factory()->create();
        $anotherDialog = DialogModel::factory()
            ->private()
            ->for($this->anotherUser, 'createdBy')
            ->addUsersToDialog([$anotherUser])
            ->withMessagesFrom([$this->anotherUser], 2)
            ->create();

        $response = $this->get(route('dialog', ['dialogId' => $anotherDialog->dialog_id]));
        $response->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }
}

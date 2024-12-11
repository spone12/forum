<?php

namespace Tests\Feature\Notation;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Notation\NotationModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\User;

class NotationTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @var string */
    const PATH = '/notation/';

    /** @var string */
    const PATH_VIEW = '/notation/view/';

    /** @var object */
    private $notationObject;

    /** @var object */
    private $user;

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->notationObject = NotationModel::factory()
             ->create(['user_id' => $this->user->id]);
    }

    /**
     * Test of notation appearance on the homepage success
     *
     * @return void
     */
    public function testNotationAppearanceOnHomepage(): void
    {

        $response = $this->get(route('homePage'));
        $response->assertOk()
                 ->assertSee(self::PATH_VIEW . $this->notationObject->notation_id);
    }

    /**
     * Test notation success
     *
     * @covers \App\Http\Controllers\NotationController::notation
     * @return void
     */
    public function testNotationSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(self::PATH);
        $response->assertOk()
                 ->assertViewIs('menu.Notation.notation');
    }

    /**
     * Test notation view success
     *
     * @covers \App\Http\Controllers\NotationController::notationView
     * @return void
     */
    public function testNotationViewSuccess(): void
    {

        $response = $this->get(self::PATH_VIEW . $this->notationObject->notation_id);
        $response
            ->assertOk()
            ->assertViewIs('menu.Notation.notationView')
            ->assertViewMissing('error')
            ->assertViewHasAll([
                'view'
            ]);
    }

    /**
     * Test create notation success
     *
     * @covers \App\Http\Controllers\NotationController::createNotation
     * @return void
     */
    public function testNotationCreateSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(self::PATH, [
            'notationName' => $this->faker->paragraph(1),
            'notationText' => $this->faker->realText(rand(100, 500))
        ]);

        $response->assertOk()
                ->assertJsonStructure([
                    'notationData' => [
                        'notationId',
                        'expAdded'
                    ]
                ]);
    }

    /**
     * Test retrieving the notation modification page
     *
     * @covers \App\Http\Controllers\NotationController::notationEditView
     * @return void
     */
    public function testNotationEditViewSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(self::PATH . 'edit/' . $this->notationObject->notation_id);
        $response->assertOk()
                ->assertViewIs('menu.Notation.notationEdit')
                ->assertViewMissing('error')
                ->assertViewHasAll([
                    'notationData',
                    'notationPhoto',
                ]);
    }

    /**
     * Test notation update success
     *
     * @covers \App\Http\Controllers\NotationController::notationUpdate
     * @return void
     */
    public function testNotationUpdateSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->putJson(self::PATH . 'update/' . $this->notationObject->notation_id, [
            'notationId'   => $this->notationObject->notation_id,
            'notationName' => $this->faker->paragraph(1),
            'notationText' => $this->faker->realText(rand(100, 500))
        ],
       [
            'X-Requested-With' => 'XMLHttpRequest'
       ]);

       $response->assertOk()
                ->assertJson([
                    'success' => true
                ]);
    }

    /**
     * Test notation change rating success
     *
     * @covers \App\Http\Controllers\NotationController::notationRating
     * @return void
     */
    public function testNotationRatingSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->postJson(self::PATH . 'rating/' . $this->notationObject->notation_id, [
            'notation_id' => $this->notationObject->notation_id,
            'action'      => $this->faker->numberBetween(0, 1)
        ],
       [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk()
                ->assertJsonStructure([
                    'success'
                ]);
    }

    /**
     * Test notation delete success
     *
     * @covers \App\Http\Controllers\NotationController::notationDelete
     * @return void
     */
    public function testNotationDeleteSuccess(): void
    {
        $this->actingAs($this->user);
        $response = $this->deleteJson(self::PATH . 'delete/' . $this->notationObject->notation_id, [
            'notation_id' => $this->notationObject->notation_id
        ],
       [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk()
                ->assertJson([
                    'success' => true
                ]);
    }

    /**
     * Test notation add photo success
     *
     * @covers \App\Http\Controllers\NotationController::notationAddPhoto
     * @return void
     */
    public function testNotationAddPhotoSuccess(): void
    {
        $this->actingAs($this->user);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg');
        $response = $this->post(self::PATH . 'add_photos/' . $this->notationObject->notation_id, [
            'images' => [
                $file
            ]
        ]);

        $this->user->fresh();

        Storage::disk('public')->assertExists(
            User::with('notationPhoto')->first()->notationPhoto->first()->path_photo
        );
        $response->assertRedirect();
    }

    /**
     * Test notation remove photo success
     *
     * @covers \App\Http\Controllers\NotationController::removeNotationPhoto
     * @return void
     */
    public function testRemoveNotationPhotoSuccess(): void
    {
        $this->actingAs($this->user);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg');
        $this->post(self::PATH . 'add_photos/' . $this->notationObject->notation_id, [
            'images' => [
                $file
            ]
        ]);

        $this->user->fresh();

        $photoObj = User::with('notationPhoto')->first()->notationPhoto->first();
        Storage::disk('public')->assertExists(
            $photoObj->path_photo
        );

        $response = $this->delete(self::PATH . 'delete_photo/' . $this->notationObject->notation_id, [
            'notationId' => $this->notationObject->notation_id,
            'photoId'    => $photoObj->notation_photo_id
        ]);

        $response->assertOk()
                ->assertJson([
                    'success' => true
                ]);
    }
}

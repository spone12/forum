<?php

namespace Tests\Feature\Notation;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Notation\NotationModel;
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
            ->assertViewMissing('error');
    }
}

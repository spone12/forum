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
    use DatabaseTransactions;

    /** @var string */
    const PATH = '/notation/';

    /** @var object */
    private $notationObject;

    protected function setUp(): void {
        parent::setUp();

        $user = User::factory()->create();
        $this->notationObject = NotationModel::factory()
            ->create(['user_id' => $user->id]);
    }

    /**
     * Test of notation appearance on the homepage
     *
     * @return void
     */
    public function testNotationGetSuccess()
    {

        $response = $this->get('/');
        $response->assertSee(self::PATH . 'view/' . $this->notationObject->notation_id);
        $response->assertStatus(200);
    }
}

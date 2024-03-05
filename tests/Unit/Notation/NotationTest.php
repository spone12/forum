<?php

namespace Tests\Unit\Notation;

use App\Enums\ResponseCodeEnum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class NotationTest
 *
 * @package Tests\Unit\Notation
 */
class NotationTest extends TestCase
{
    /** @var $id */
    protected $id;

    /**
     *
     * @return void
    */
    public function testNotationId():void
    {

        $this->id = rand(1, 5);
        $this->get(route('notation_view_id', $this->id))
            ->assertStatus(ResponseCodeEnum::OK);
    }

    /**
     *
     * @return void
    */
    public function testNotationWrong():void
    {

        $this->get(route('notation_view_id', 'bad'))
        ->assertStatus(ResponseCodeEnum::NOT_FOUND);
    }
}

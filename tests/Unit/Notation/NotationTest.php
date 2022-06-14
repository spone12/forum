<?php

namespace Tests\Unit\Notation;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotationTest extends TestCase
{
    protected $id;

    public function testNotationId()
    {
        $this->id =  rand(1, 5);
        $this
            ->get(route('notation_view_id',  $this->id))
            ->assertStatus(200);
    }

    public function testNotationWrong()
    {
        $this
        ->get(route('notation_view_id', 'bad'))
        ->assertStatus(404);
    }

}


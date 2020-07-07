<?php

namespace Tests\Unit\Notation;

use PHPUnit\Framework\TestCase;

class NotationTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function testNotationId()
    {
        $id = rand(1, 50);

        $this
            ->get(route('notation_view_id'), ['id' => $id])
            ->assertStatus(200);
    }

    public function testNotation()
    {
        $this
            ->get(route('notation'))
            ->assertStatus(200);
    }
}

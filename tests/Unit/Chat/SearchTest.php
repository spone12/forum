<?php

namespace Chat;

use PHPUnit\Framework\TestCase;
use App\Service\Chat\ChatSearchService;
use App\DTO\Chat\SearchDTO;
use Illuminate\Foundation\Testing\WithFaker;
use App\Contracts\Chat\ChatMessageSearchInterface;
use Illuminate\Support\Collection;
use Mockery;

class SearchTest extends TestCase
{
    use WithFaker;

    /**
     * @covers \App\Service\Chat\ChatSearchService::searchAll
     * @return void
     */
    public function test_chat_search_all_calls_repository()
    {
        $dto = new SearchDTO('hello');

        $repositoryMock = Mockery::mock(ChatMessageSearchInterface::class);
        $repositoryMock->shouldReceive('searchAll')
            ->once()
            ->with('hello')
            ->andReturn(
                collect(['message'])
            );

        $service = new ChatSearchService($repositoryMock);

        $result = $service->searchAll($dto);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['message'], $result->all());
    }
}

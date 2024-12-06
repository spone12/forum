<?php

namespace Tests\Feature\Api;

use App\Enums\ResponseCodeEnum;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User as User;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @var object $user*/
    private $user;

    /** @var string $apiKey*/
    private $apiKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $responseAPIKey = $this->put('/profile/generate_api_key');
        $this->apiKey = json_decode($responseAPIKey->getContent())->api_key;
    }

    /**
     * Update Token Fail
     *
     * @covers \App\Http\Controllers\Api\ApiController::updateToken
     * @return void
     */
    public function testUpdateTokenFail():void
    {
        $response = $this->withHeaders(
            [
                'Content-Type' => 'Application/json',
            ]
        )->put('/api/update_token', ['api_key' => 'test']);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN)
            ->assertJson(['error' => true]);
    }

    /**
     * Generate API Key success
     *
     * @covers \App\User::generateApiKey
     * @return void
     */
    public function testGenerateApiKeySuccess():void
    {
        $response = $this->put('/profile/generate_api_key');
        $response->assertOk()
            ->assertJson(['api_key' => true]);
    }

    /**
     * Update Token Success
     *
     * @covers \App\Http\Controllers\Api\ApiController::updateToken
     * @return void
     */
    public function testUpdateTokenSuccess():void
    {
        $response = $this->withHeaders(
            [
                'Content-Type' => 'Application/json',
            ]
        )->json(
            'PUT', route(
                'updateToken',
                [
                        'api_key' => $this->apiKey,
                        'update_token' => true
                    ]
            )
        );

        $response->assertOk()
            ->assertJson(['api_token' => true]);
    }
}

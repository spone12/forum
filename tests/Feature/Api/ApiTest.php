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
    //protected $seeder = User::class;
    use RefreshDatabase;
    /** @var $user */
    private $user;
    /** @var $apiKey */
    private $apiKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
        $response = $this->put('/generate_api_key');
        $this->apiKey = DB::table('users')
             ->select('api_key')
             ->where('id', '=', $this->user->id)
         ->get();
        $this->apiKey = $this->apiKey[0]->api_key;
    }

    /**
     * Update Token Fail
     * @return void
    */
    public function testUpdateTokenFail():void
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->put('/api/update_token', ['api_key' => 'test']);

        $response->assertStatus(ResponseCodeEnum::FORBIDDEN)
            ->assertJson(['error' => true]);
    }

    /**
     * Generate api key
     * @return void
    */
    public function testGenerateApiKey():void
    {

        $response = $this->put('/generate_api_key');
        $response->assertStatus(ResponseCodeEnum::OK)
            ->assertJson(['api_key' => true]);
    }

    /**
     * Update Token Success
     * @return void
    */
    public function testUpdateTokenSuccess():void
    {

        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('PUT', route('updateToken',
        [
            'api_key' => $this->apiKey,
            'update_token' => true
        ]));

        $response->assertStatus(ResponseCodeEnum::OK)
            ->assertJson(['api_token' => true]);
    }
}

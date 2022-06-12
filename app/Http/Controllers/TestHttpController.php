<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestHttpController extends Controller
{
    protected function http() {

        return $this->guzzleCreate('put', '/api/update_token');
    }

    protected function guzzleCreate($method = 'get', $address, $queryParams = [], $headers = [], $baseUri = 'http://l.forum') {

        $client = new \GuzzleHttp\Client([
            'base_uri' => $baseUri
        ]);

        /*$response = $client->$method($address, [
            'headers' => [
                'Content-Type' => 'Application/json',
                'Authorization' => 'Bearer 5cQBrccDy52sukVC68dtj0Eevvv6U4KvXTXmAQeVFx6Jw9eURdL64PHwZn6SfhkhIgv6d676x4QOgS1R',
            ],
            'query' => [
                'api_key' => 'd371969b1129da8fbc6e'
            ],
        ]);*/

        $response = $client->$method($address, [
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
            'query' => [
                'api_key' => 'd371969b1129da8fbc6e',
                'update_token' => true,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestHttpController extends Controller
{
    protected function http() {

        return $this->guzzleCreate();
    }

    protected function guzzleCreate($queryParams = [], $headers = [], $baseUri = 'http://l.forum') {

        $client = new \GuzzleHttp\Client([
            'base_uri' => $baseUri
        ]);

        $response = $client->post('/api/v1/notation/list', [
            'headers' => [
                'Content-Type' => 'Application/json',
                'Authorization' => 'Bearer 5cQBrccDy52sukVC68dtj0Eevvv6U4KvXTXmAQeVFx6Jw9eURdL64PHwZn6SfhkhIgv6d676x4QOgS1R',
            ],
            'query' => [
                'api_key' => 'd371969b1129da8fbc6e'
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}

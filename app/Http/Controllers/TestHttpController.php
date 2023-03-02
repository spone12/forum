<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestHttpController extends Controller
{
    /**
     * @return mixed
     */
    protected function http() {

        return $this->guzzleCreate('post', '/api/v1/notation/list');
    }

    /**
     * @param string $method
     * @param $address
     * @param array $queryParams
     * @param array $headers
     * @param string $baseUri
     * @return mixed
     */
    protected function guzzleCreate($method = 'get', $address, $queryParams = [], $headers = [], $baseUri = 'http://l.forum') {

        $client = new \GuzzleHttp\Client([
            'base_uri' => $baseUri
        ]);

        $response = $client->$method($address, [
            'headers' => [
                'Content-Type' => 'Application/json',
                'Authorization' => 'Bearer dT24bymNzHjnsgnx5KsGGkWOx6w3c5s9oejlWATu7AibTOIauIXtUCyr3vmxuzLhUWZzRtU6eQUsB6wP',
            ],
            'query' => [
                'api_key' => '4bb5dad0cfee460303f4'
            ],
        ]);

       /* $response = $client->$method($address, [
            'headers' => [
                'Content-Type' => 'Application/json',
            ],
            'query' => [
                'api_key' => '4bb5dad0cfee460303f4',
                'update_token' => true,
            ],
        ]);*/

        return json_decode($response->getBody(), true);
    }
}

<?php

namespace Modules\Users\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TrafficService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function handle($authorization, $payload)
    {
        $url =  config('app.api_domain') . '/admin/api/index.php/api/user/traffic';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $authorization,
                ],
                'body' => json_encode(['payload' =>$payload])
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            return [
                'error' => 'Failed to load traffic data',
                'status' => $statusCode
            ];
        }
    }
}

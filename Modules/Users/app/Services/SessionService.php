<?php

namespace Modules\Users\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SessionService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle($payload,$authorization, $id, $page = 1)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/UserSessions/' . $id . '?page=' . $page;
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $authorization,
                ],
                'body' => json_encode(["payload" => $payload])
            ]);
            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            return [
                'error' => 'Failed to fetch user sessions',
                'status' => $statusCode
            ];
        }
    }
}

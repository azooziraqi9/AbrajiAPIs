<?php

namespace Modules\Manager\Services;

use GuzzleHttp\Client;
use Modules\Manager\Interfaces\IMangerService;

class MangerService implements IMangerService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function GetManger($authorization)
    {
        try {
            $url = config('app.api_domain') . '/admin/api/index.php/api/index/manager';
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $authorization,
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle 4xx errors like 401 Unauthorized
            if ($e->getResponse()->getStatusCode() === 401) {
                return (object) [
                    'status' => 401,
                    'error' => 'Unauthorized: Invalid token provided.'
                ];
            }

            // Handle other client exceptions
            return (object) [
                'status' => $e->getResponse()->getStatusCode(),
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Handle any other errors
            return (object) [
                'status' => 500,
                'error' => 'An unexpected error occurred: ' . $e->getMessage()
            ];
        }
    }

}

<?php

namespace Modules\Card\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class HandleService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendRequest($method, $url, $headers = [], $body = null)
    {
        try {
            $options = [
                'headers' => $headers,
            ];

            if ($body) {
                $options['body'] = json_encode($body);
            }

            $response = $this->client->request($method, $url, $options);
            $responseData = json_decode($response->getBody()->getContents(), true);

            return [
                'status' => $response->getStatusCode(),
                'data' => $responseData,
            ];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Client error: ' . $e->getMessage(), [
                'url' => $url,
                'request' => $options,
                'response' => $e->getResponse() ? (string) $e->getResponse()->getBody() : 'No response body'
            ]);

            return [
                'status' => 401,
                'data' => null,
                'error' => 'Unauthorized. Please check your authorization token.'
            ];
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage(), [
                'url' => $url,
                'request' => $options
            ]);

            return [
                'status' => 500,
                'data' => null,
                'error' => 'An error occurred. Please try again later.'
            ];
        }
    }
}

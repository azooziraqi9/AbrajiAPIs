<?php

namespace Modules\Dashboard\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Modules\Card\Services\HandleService;

class DashboardCardService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function handle($authorization, $payload)
    {
        try {
            $response = $this->fetchCardsSeries($authorization, $payload);
            if (isset($response['data'])) {
                return [
                    'cards' => $this->makeCardStats($response['data']['data']),
                    'status' => 200,
                ];
            } else {
                return [
                    'error' => $response['error'],
                    'status' => $response['status'],
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    private function makeCardStats($data)
    {
        $cards = [];
        foreach ($data as $card) {
            if (isset($card['profile_details']) && is_array($card['profile_details'])) {
                $profileName = $card['profile_details']['name'];
                if (!isset($cards[$profileName])) {
                    $cards[$profileName] = [
                        'id' => $card['profile_details']['id'],
                        'profile' => $profileName,
                        'total' => 0,
                        'used' => 0,
                        'remaining' => 0,
                    ];
                }
                $cards[$profileName]['total'] += $card['qty'];
                $cards[$profileName]['used'] += $card['used'];
                $cards[$profileName]['remaining'] += ($card['qty'] - $card['used']);
            }
        }

        return array_values($cards);
    }

    private function fetchCardsSeries($authorization, $payload)
    {
        $apiClient = new HandleService();
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/series';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $authorization,
        ];

        $body = ['payload' => $payload];

        $response = $apiClient->sendRequest('POST', $url, $headers, $body);

        if ($response['status'] === 200) {
            return [
                'data' => $response['data'],
                'status' => $response['status'],
            ];
        } else {
            return [
                'error' => $response['error'],
                'status' => $response['status'],
            ];
        }
    }
}

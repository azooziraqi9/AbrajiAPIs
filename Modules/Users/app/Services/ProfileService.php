<?php

namespace Modules\Users\Services;

use GuzzleHttp\Client;
use Modules\Users\Interfaces\ProfileServiceInterface;

class ProfileService implements ProfileServiceInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getServices($id, $authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/list/profile/' . $id;
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function changeUserService($payload, $authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/changeProfile';
        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $payload])
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getManagerTree($authorization)
    {
        // TODO: Implement getManagerTree() method. url is "/admin/api/index.php/api/manager/tree"
        $url = config('app.api_domain') . '/admin/api/index.php/api/manager/tree';
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);

    }

    public function changeProfile($payload, $authorization)
    {
        // TODO: Implement changeProfile() method post request to "https://sas.nbtel.iq:/admin/api/index.php/api/user/changeProfile"
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/changeProfile';
        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $payload])
        ]);
        return json_decode($response->getBody()->getContents(), true);

    }

    public function getActiveData($id, $authorization)
    {
        // TODO: Implement getActiveData() method get request to "https://sas.nbtel.iq/admin/api/index.php/api/user/activationData/ID"
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/activationData/' . $id;
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);


    }
}

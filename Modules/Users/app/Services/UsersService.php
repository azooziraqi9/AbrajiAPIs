<?php

namespace Modules\Users\Services;

use GuzzleHttp\Client;
use Modules\Users\Interfaces\UsersServiceInterface;

class UsersService implements UsersServiceInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getUsersTable(array $data, $authorization,$page)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/user';
        $response = $this->client->post($url . ($page ? '?page=' . $page : ''), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $data['payload']])
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getOnlineUsers(array $data,$authorization, $page)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/online';
        $response = $this->client->post($url . ($page ? '?page=' . $page : ''), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $data['payload']])
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createUser(array $data,$authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user';
        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $data['payload']])
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUserById($id, $authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/' . $id;
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ]
        ])->getBody()->getContents();
        $response=$this->userOverview($id, $authorization, json_decode($response, true));
        return $response;
    }

    public function editUser($id, array $data,$authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/' . $id;
        $response = $this->client->put($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $data['payload']])
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function disconnectUser($id, $authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/disconnect/userid/' . $id;
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
    public function usersActivate($data, $authorization)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/activate';
        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
            'body' => json_encode(['payload' => $data['payload']])
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
    private function userOverview($id, $authorization,$data)
    {
        $url = config('app.api_domain') . '/admin/api/index.php/api/user/overview/' . $id;
        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
            ],
        ])->getBody()->getContents();
        $data['overview']=json_decode($response, true)["data"];
        return $data;
    }
}

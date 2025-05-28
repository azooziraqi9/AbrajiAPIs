<?php

namespace Modules\Users\Interfaces;

interface UsersServiceInterface
{
    public function getUsersTable(array $data,$authorization, $page);
    public function getOnlineUsers(array $data,$authorization, $page);
    public function createUser(array $data,$authorization);
    public function getUserById($id, $authorization);
    public function editUser($id, array $data,$authorization);
    public function disconnectUser($id, $authorization);
    public function usersActivate($data, $authorization);
}

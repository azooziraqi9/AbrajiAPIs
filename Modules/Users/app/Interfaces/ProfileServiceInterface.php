<?php

namespace Modules\Users\Interfaces;

interface ProfileServiceInterface
{
    public function getServices($id,$authorization);
    public function changeUserService( $payload,$authorization);
    //get manger tree
    public function getManagerTree($authorization);

    //changeProfile
    public function changeProfile($payload,$authorization);

    // get active data by user id
    public function getActiveData($id,$authorization);
}

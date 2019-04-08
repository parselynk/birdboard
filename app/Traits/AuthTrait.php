<?php
namespace App\Traits;

trait AuthTrait
{
    public function authorizeUser()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        return $user;
    }

    public function createUser()
    {
        return factory('App\User')->create();
    }
}

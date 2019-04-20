<?php
namespace App\Traits;

use App\User;

trait AuthTrait
{
    public function authorizeUser(User $user = null)
    {
        
        $newUser = $user ?: $this->createUser();
        $this->actingAs($newUser);

        return $newUser;
    }

    public function createUser()
    {
        return factory(User::class)->create();
    }
}

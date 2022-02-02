<?php

namespace App\Tests\Traits;

use App\Entity\User;

trait UserTrait
{


    protected function getUser(User $user): User
    {
        $user->setUsername('Test username');
        $user->setPassword('test_password');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail('test@test.com');
        return $user;

    }
}

<?php

namespace Kanban\Factories;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    public static function buildAdminWithUsername(string $username)
    {
        return self::buildAdmin($username);
    }

    public static function buildAdmin($username = 'admin') : User
    {
        $user = new User();

        $user->setUsername($username);
        $user->setPassword(md5('password'));
        $user->setEmail('sensorario@gmail.com');

        return $user;
    }
}

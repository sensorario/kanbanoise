<?php

namespace Kanban\Factories;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    public static function buildAdmin() : User
    {
        $user = new User();

        $user->setUsername('admin');
        $user->setPassword(md5('password'));
        $user->setEmail('sensorario@gmail.com');

        return $user;
    }
}

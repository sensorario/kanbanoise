<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Kanban\Factories\UserFactory;

class Users extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $admin = UserFactory::buildAdmin();
        $manager->persist($admin);
        $manager->flush();
    }
}

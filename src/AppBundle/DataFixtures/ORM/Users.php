<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Kanban\Factories;

class Users extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $admin = Factories\UserFactory::buildAdmin();
        $manager->persist($admin);
        $manager->flush();

        $board = Factories\BoardFactory::buildWithOwner('admin');
        $manager->persist($board);
        $manager->flush();

        $defaultStatuses = [[
            'name' => 'Backlog',
            'wip'  => 0,
        ], [
            'name' => 'To Do',
            'wip'  => 0,
        ], [
            'name' => 'In Progress',
            'wip'  => 3,
        ], [
            'name' => 'Done',
            'wip'  => 0,
        ]];

        foreach ($defaultStatuses as $itemValue) {
            $status = Factories\StatusFactory::buildWithNameAndWipLimit(
                $itemValue['name'],
                $itemValue['wip']
            );

            $manager->persist($status);
            $manager->flush();
        }

        $status = Factories\CardTypeFactory::buildWithName('task');
        $manager->persist($status);
        $manager->flush();

        $project = Factories\ProjectFactory::buildWithNameAndOwner('kanbanoise', 'admin');
        $manager->persist($project);
        $manager->flush();
    }
}

<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Kanban\Factories;

class Fixtures extends Fixture
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
            'wip'  => null,
        ], [
            'name' => 'To Do',
            'wip'  => null,
        ], [
            'name' => 'In Progress',
            'wip'  => 3,
        ], [
            'name' => 'Done',
            'wip'  => null,
        ]];

        foreach ($defaultStatuses as $itemValue) {
            $status = Factories\StatusFactory::buildWithNameAndWipLimit(
                $itemValue['name'],
                $itemValue['wip']
            );

            $manager->persist($status);
            $manager->flush();
        }

        $taskStatus = Factories\CardTypeFactory::buildWithName('task');
        $manager->persist($taskStatus);
        $manager->flush();

        $bugStatus = Factories\CardTypeFactory::buildWithName('bug');
        $manager->persist($bugStatus);
        $manager->flush();

        $project = Factories\ProjectFactory::buildWithNameAndOwner('kanbanoise', 'admin');
        $manager->persist($project);
        $manager->flush();
    }
}

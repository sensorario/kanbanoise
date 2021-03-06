<?php

namespace AppBundle\Component;

class Installer
{
    private $manager;

    public function __construct(
        \Doctrine\ORM\EntityManagerInterface $manager
    ) {
        $this->manager = $manager;
    }

    public function verify()
    {
        if (!file_exists(__DIR__ . '/../../../var/data/data.sqlite')) {
            echo '<li>database not exists';
            echo '<li>php ./bin/console doctrine:database:create';
            die;
        }

        try {
            $users = $this->manager->getRepository(\AppBundle\Entity\User::class)
                ->findAll();
        } catch(\Doctrine\DBAL\Exception\TableNotFoundException $exception) {
            echo '<li>tables not found';
            echo '<li>php ./bin/console doctrine:schema:update --force';
            die;
        }

        $boards = $this->manager->getRepository(\AppBundle\Entity\Board::class)
            ->findAll();
        if (count($boards) == 0) {
            echo '<li>no boards found';
            echo '<li>php ./bin/console doctrine:query:sql "insert into board values (null, \'admin\', 4);"';
            die;
        }

        $statuses = $this->manager->getRepository(\AppBundle\Entity\Status::class)
            ->findAll();
        if (count($statuses) == 0) {
            echo '<li>no status found';
            echo '<li>php ./bin/console doctrine:query:sql "insert into status values (null, \'todo\', 1, null);"';
            die;
        }

        $cardTypes = $this->manager->getRepository(\AppBundle\Entity\CardType::class)
            ->findAll();
        if (count($cardTypes) == 0) {
            echo '<li>any card type found';
            echo '<li>php ./bin/console doctrine:query:sql "insert into card_type values (null, \'task\');"';
            die;
        }

        if (count($users) == 0) {
            echo '<li>no user found';
            echo '<li>php ./bin/console doctrine:query:sql "insert into user values (null, \'admin\', \'password\', \'email\', 1);"';
            die;
        }
    }
}

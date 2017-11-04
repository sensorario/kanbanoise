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
            echo '<li>database non esiste';
            echo '<li>php ./bin/console doctrine:database:create';
            die;
        }

        try {
            $users = $this->manager->getRepository(\AppBundle\Entity\User::class)
                ->findAll();
        } catch(\Doctrine\DBAL\Exception\TableNotFoundException $exception) {
            echo '<li>tabelle non trovate';
            echo '<li>php ./bin/console doctrine:schema:update --force';
            die;
        }

        if (count($users) == 0) {
            echo '<li>nessun utente trovato';
            echo '<li>php ./bin/console doctrine:query:sql "insert into user values (null, \'admin\', \'password\', \'email\', 1);"';
            die;
        }
    }
}

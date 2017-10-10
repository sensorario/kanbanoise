<?php

namespace AppBundle\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

class AppContext implements Context
{
    public function __construct()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @Given the database is clean
     */
    public function thereAreNoUsersInDatabase()
    {
        $this->manager->createQuery('delete from AppBundle\Entity\Member')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Card')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Status')->execute();
    }

    /**
     * @Given exists one card
     */
    public function existsOneCard()
    {
        $card = new \AppBundle\Entity\Card();
        $card->setTitle('sample card');
        $card->setDescription('this card do nothing');
        $card->setStatus('status');
        $card->setType('type');
        $this->manager->persist($card);
        $this->manager->flush();
    }
}

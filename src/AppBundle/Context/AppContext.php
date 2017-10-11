<?php

namespace AppBundle\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

class AppContext implements Context
{
    public function __construct()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
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
        $this->buildOneCard();
    }

    /**
     * @Given exists member :name
     */
    public function existsMember($name)
    {
        $member = new \AppBundle\Entity\Member();
        $member->setName($name);
        $this->manager->persist($member);
        $this->manager->flush();
    }

    /**
     * @Given exists one card assigned to :name
     */
    public function existsOneCardAssignedTo($name)
    {
        $this->buildOneCard();
        $this->card->setMember($name);
        $this->manager->persist($this->card);
        $this->manager->flush();
    }

    private function buildOneCard()
    {
        $this->card = new \AppBundle\Entity\Card();
        $this->card->setTitle('sample card');
        $this->card->setDescription('this card do nothing');
        $this->card->setStatus('todo');
        $this->card->setType('task');

        $this->manager->persist($this->card);
        $this->manager->flush();
    }

    /**
     * @Given exists status :arg1
     */
    public function existsStatus($arg1)
    {
        $this->status = new \AppBundle\Entity\Status();
        $this->status->setName('todo');
        $this->status->setPosition(1);

        $this->manager->persist($this->status);
        $this->manager->flush();
    }
}

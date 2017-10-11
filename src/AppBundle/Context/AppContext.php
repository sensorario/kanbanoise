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
        $this->buildStatus('todo', null);
    }

    private function buildStatus($name, $wipLimit)
    {
        $this->status = new \AppBundle\Entity\Status();
        $this->status->setName($name);
        $this->status->setPosition(1);
        $this->status->setWipLimit($wipLimit);

        $this->manager->persist($this->status);
        $this->manager->flush();
    }

    /**
     * @When exists status :statusName with wip limit :statusWipLimit
     */
    public function existsStatusWithWipLimit($statusName, $statusWipLimit)
    {
        $this->buildStatus($statusName, $statusWipLimit);
    }

    /**
     * @When /^I check the "([^"]*)" radio button$/
     */
    public function iCheckTheRadioButton($labelText)
    {
        $page = $this->getSession()->getPage();
        $radioButton = $page->find('named', ['radio', $labelText]);
        if ($radioButton) {
            $select = $radioButton->getAttribute('name');
            $option = $radioButton->getAttribute('value');
            $page->selectFieldOption($select, $option);
            return;
        }

        throw new \Exception("Radio button with label {$labelText} not found");
    }
}

<?php

namespace AppBundle\Context;

use AppContext\Entity\Status;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Kanban\Factories;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AppContext implements Context
{
    public function __construct()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->session = $this->container->get('session');
    }

    /**
     * @Given the database is clean
     */
    public function thereAreNoUsersInDatabase()
    {
        $this->manager->createQuery('delete from AppBundle\Entity\Member')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Card')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Status')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Board')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\User')->execute();
    }

    /**
     * @Given exists one card with status :statusName
     */
    public function existsOneCardWithStatus(string $statusName)
    {
        $status = $this->catchStatusWithName($statusName);

        if (!$status) {
            throw new \RuntimeException(
                'Oops! Status was never created'
            );
        }

        $this->buildOneCardWithStatus($status);
    }

    /**
     * @Given exists admin user
     */
    public function existsAdminUser()
    {
        $admin = Factories\UserFactory::buildAdmin();

        $this->manager->persist($admin);
        $this->manager->flush();
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
     * @Given exists :amount card with status :statusName assigned to :name
     */
    public function existSomeCardsAssignedTo($amount, $statusName, $name)
    {
        $status = $this->getStatusWithName($statusName);

        for ($i = 0; $i <= $amount; $i++) {
            $card = $this->buildOneCardWithStatus($status);
            $card->setMember($name);
            $this->manager->persist($card);
            $this->manager->flush();
        }
    }

    private function buildOneCardWithStatus(
        \AppBundle\Entity\Status $status
    ) : \AppBundle\Entity\Card {
        $card = Factories\CardFactory::buildWithStatus($status);

        $this->manager->persist($card);
        $this->manager->flush();

        return $card;
    }

    /**
     * @Given exists status :statusName
     */
    public function getStatusWithName($statusName)
    {
        $status = $this->catchStatusWithName($statusName);

        if (!$status) {
            return $this->buildStatus($statusName, null);
        }

        return $status;
    }

    private function buildStatus($name, $wipLimit)
    {
        $status = Factories\StatusFactory::buildWithNameAndWipLimit($name, $wipLimit);

        $this->manager->persist($status);
        $this->manager->flush();

        return $status;
    }

    /**
     * @When exists status :statusName with wip limit :statusWipLimit
     */
    public function getStatusWithNameWithWipLimit($statusName, $statusWipLimit)
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

    /**
     * @Given exists a board with wip limit of :wip
     */
    public function existsABoard($wip)
    {
        $this->board = new \AppBundle\Entity\Board();
        $this->board->setOwner('sensorario');
        $this->board->setWipLimit($wip);
        $this->manager->persist($this->board);
        $this->manager->flush();
    }

    /**
     * @Given exists status :name without wip limit
     */
    public function getStatusWithNameWithoutWipLimit($name)
    {
        $status = Factories\StatusFactory::buildWithNameAndWipLimit($name, null);

        $this->manager->persist($status);
        $this->manager->flush();
    }

    /**
     * @Given I am logged in as admin
     */
    public function iAmLoggedInAsAdmin()
    {
        $admin = $this->manager
            ->getRepository(\AppBundle\Entity\User::class)
            ->loadUserByUsername('admin');
        $token = new UsernamePasswordToken($admin, null, 'main', ['ROLE_ADMIN']);
        $this->session->set('_security_main', serialize($token));
    }

    public function catchStatusWithName(string $statusName)
    {
        return $this->manager
            ->getRepository(\AppBundle\Entity\Status::class)
            ->findOneByName($statusName);
    }

    /**
     * @Given the board have limit :wipLimit
     */
    public function theBoardHaveLimit($wipLimit)
    {
        $this->board->setWipLimit($wipLimit);
        $this->manager->persist($this->board);
        $this->manager->flush();
    }
}

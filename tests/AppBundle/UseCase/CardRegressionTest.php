<?php

namespace Tests\AppBundle\UseCase;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;
use AppBundle\UseCase\CardRegression;
use Kanban\Factories\CardFactory;
use PHPUnit\Framework\TestCase;

class CardControllerTest extends TestCase
{
    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');

        $this->finder = $this
            ->getMockBuilder('AppBundle\Actors\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistor = $this
            ->getMockBuilder('AppBundle\Actors\Persistor')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testMoveCardToPreviousStatusWhenExistsOnlyTwoStatuses()
    {
        $this->givenDatabaseIsClean();
        $this->andSomeStatusesExists([
            'statuses' => [
                [
                    'name' => 'foooo',
                    'position' => 1,
                ], [
                    'name' => 'bar',
                    'position' => 2,
                ]
            ]
        ]);
        $this->andACardExistsInStatus('bar');

        $this->finder->expects($this->once())
            ->method('findByName')
            ->with('bar')
            ->willReturn(\AppBundle\Entity\Status::fromArray([
                'name' => 'bar',
                'position' => '2',
            ]));

        $this->finder->expects($this->once())
            ->method('findByPosition')
            ->with(1)
            ->willReturn($newStatus = \AppBundle\Entity\Status::fromArray([
                'name' => 'foooo',
                'position' => '1',
            ]));

        $this->persistor->expects($this->once())
            ->method('save');

        $cardRegression = new CardRegression(
            $this->finder,
            $this->persistor
        );
        $cardRegression->setCard($this->card);
        $cardRegression->execute();

        $this->manager->getRepository('AppBundle:Card')
            ->findBy([
                'id' => $this->card->getId(),
            ]);

        $this->assertEquals('foooo', $this->card->getStatus());
    }

    public function testWhenStatusIsFirstCardWillNotChangeItsStatus()
    {
        $this->givenDatabaseIsClean();
        $this->andSomeStatusesExists([
            'statuses' => [
                [
                    'name' => 'foooo',
                    'position' => 1,
                ], [
                    'name' => 'bar',
                    'position' => 2,
                ]
            ]
        ]);
        $this->andACardExistsInStatus('foooo');

        $this->finder->expects($this->once())
            ->method('findByName')
            ->with('foooo')
            ->willReturn(\AppBundle\Entity\Status::fromArray([
                'name' => 'foooo',
                'position' => '1',
            ]));

        $cardRegression = new CardRegression(
            $this->finder,
            $this->persistor
        );
        $cardRegression->setCard($this->card);
        $cardRegression->execute();

        $this->manager->getRepository('AppBundle:Card')
            ->findBy([
                'id' => $this->card->getId(),
            ]);

        $this->assertEquals('foooo', $this->card->getStatus());
    }

    public function andSomeStatusesExists(array $conf)
    {
        $statuses = $conf['statuses'];

        foreach ($statuses as $status) {
            $newStatus = new Status();
            $newStatus->setName($status['name']);
            $newStatus->setPosition($status['position']);
            $this->manager->persist($newStatus);
            $this->manager->flush();
        }
    }

    public function givenDatabaseIsClean()
    {
        $this->manager->createQuery('delete from AppBundle\Entity\Card')->execute();
        $this->manager->createQuery('delete from AppBundle\Entity\Status')->execute();
        $this->manager->flush();
    }

    public function andACardExistsInStatus(string $status)
    {
        $this->card = CardFactory::buildWithStatus($status);

        $this->manager->persist($this->card);
        $this->manager->flush();
    }
}

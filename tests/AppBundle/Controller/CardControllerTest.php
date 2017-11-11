<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->controller = $this->container->get(\AppBundle\Controller\CardController::class);
        $this->cardFactory = $this->container->get(\Kanban\Factories\CardFactory::class);
        $this->statusFactory = $this->container->get(\Kanban\Factories\StatusFactory::class);
    }

    public function testMoveCardsFromTheirStatusToAnotherOne()
    {
        $previousStatus = $this->statusFactory->createWithName('previousStatus');
        $nextStatus = $this->statusFactory->createWithName('nextStatus');
        $this->manager->persist($previousStatus);
        $this->manager->persist($nextStatus);
        $card = $this->cardFactory->createWithStatus($previousStatus);
        $this->manager->persist($card);

        $this->cardMover = $this
            ->getMockBuilder(\Kanban\Actors\CardMover::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cardMover->expects($this->once())
            ->method('setCard')
            ->with($card);
        $this->cardMover->expects($this->once())
            ->method('setFutureStatusName')
            ->with($nextStatus);
        $this->cardMover->expects($this->once())
            ->method('move');

        $response = $this->controller->moveAction(
            $card,
            $nextStatus,
            $this->cardMover
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['success' => true]), $response->getContent());
    }

    public function testReturnsFailureResponseWheneverMoverCantMoveCardToDestinationStatus()
    {
        $previousStatus = $this->statusFactory->createWithName('previousStatus');
        $nextStatus = $this->statusFactory->createWithName('nextStatus');
        $this->manager->persist($previousStatus);
        $this->manager->persist($nextStatus);
        $card = $this->cardFactory->createWithStatus($previousStatus);
        $this->manager->persist($card);

        $this->cardMover = $this
            ->getMockBuilder(\Kanban\Actors\CardMover::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cardMover->expects($this->once())
            ->method('setCard')
            ->with($card);
        $this->cardMover->expects($this->once())
            ->method('setFutureStatusName')
            ->with($nextStatus);
        $this->cardMover->expects($this->once())
            ->method('move')
            ->will($this->throwException(new \RuntimeException()));

        $response = $this->controller->moveAction(
            $card,
            $nextStatus,
            $this->cardMover
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['success' => false]), $response->getContent());
    }
}

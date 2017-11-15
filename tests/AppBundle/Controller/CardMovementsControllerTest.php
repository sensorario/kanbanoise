<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CardMovementsControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();
        $this->manager = $this->container->get('doctrine.orm.entity_manager');

        $this->controller = $this->container
            ->get(\AppBundle\Controller\CardMovementsController::class);

        $this->cardFactory = $this->container
            ->get(\Kanban\Factories\CardFactory::class);

        $this->statusFactory = $this->container
            ->get(\Kanban\Factories\StatusFactory::class);
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
        $this->assertEquals(json_encode([
            'success' => true,
        ]), $response->getContent());
    }

    public function testFailureWheneverCardMoverCantMoveCardToDestinationStatus()
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

    public function testRegressionRedirectUserToKanban()
    {
        $this->destinationFinder = $this
            ->getMockBuilder('Kanban\Actors\DestinationFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routing = $this
            ->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));
        $card = $this->cardFactory->createWithStatus($randomStatus);

        $response = $this->controller->regressAction(
            $card,
            $this->destinationFinder,
            $this->routing
        );

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testDontCheckColumnLimitWhenPreviousStatusNotExists()
    {
        $this->destinationFinder = $this
            ->getMockBuilder('Kanban\Actors\DestinationFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));
        $card = $this->cardFactory->createWithStatus($randomStatus);

        $this->destinationFinder->expects($this->once())
            ->method('setCard')
            ->with($card);

        $this->destinationFinder->expects($this->once())
            ->method('prevStatusFound')
            ->willReturn(false);

        $this->destinationFinder->expects($this->never())
            ->method('isColumnLimitReached');

        $response = $this->controller->regressAction(
            $card,
            $this->destinationFinder
        );

        $this->assertEquals(
            new RedirectResponse('/card/kanban'),
            $response
        );
    }

    public function testCheckColumnLimitWhenPreviousStatusExists()
    {
        $this->destinationFinder = $this
            ->getMockBuilder('Kanban\Actors\DestinationFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));
        $card = $this->cardFactory->createWithStatus($randomStatus);

        $this->destinationFinder->expects($this->once())
            ->method('setCard')
            ->with($card);

        $this->destinationFinder->expects($this->once())
            ->method('prevStatusFound')
            ->willReturn(true);

        $this->destinationFinder->expects($this->once())
            ->method('isColumnLimitReached')
            ->willReturn(false);

        $response = $this->controller->regressAction(
            $card,
            $this->destinationFinder
        );

        $this->assertEquals(
            new RedirectResponse('/card/kanban'),
            $response
        );
    }

    public function testRedirectUserToKanbanAfterProgress()
    {
        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));
        $card = $this->cardFactory->createWithStatus($randomStatus);

        $this->destinationFinder = $this
            ->getMockBuilder('Kanban\Actors\DestinationFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationFinder->expects($this->once())
            ->method('setCard')
            ->with($card);

        $this->destinationFinder->expects($this->once())
            ->method('nextStatusFound')
            ->willReturn(true);

        $this->destinationFinder->expects($this->once())
            ->method('isColumnLimitReached')
            ->willReturn(false);

        $response = $this->controller->progressAction(
            $card,
            $this->destinationFinder
        );

        $this->assertEquals(
            new RedirectResponse('/card/kanban'),
            $response
        );
    }

    public function testCloneRedirectUserToKanban()
    {
        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));

        $card = $this->cardFactory->createWithStatus($randomStatus);

        $this->entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->limitChecker = $this
            ->getMockBuilder('Kanban\Actors\LimitChecker')
            ->disableOriginalConstructor()
            ->getMock();

        $this->boardLimitChecker = $this
            ->getMockBuilder('Kanban\Actors\BoardLimitChecker')
            ->disableOriginalConstructor()
            ->getMock();

        $this->limitChecker->expects($this->once())
            ->method('setCard')
            ->with($card);

        $this->limitChecker->expects($this->once())
            ->method('setFutureStatusName')
            ->with($card->getStatus()->getName());

        $this->limitChecker->expects($this->once())
            ->method('isColumnLimitReached')
            ->will($this->returnValue(true));

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        /** I am not sire this si a good idea ... */
        $this->controller->setContainer($this->container);

        $this->boardLimitChecker->expects($this->once())
            ->method('isBoardLimitReached')
            ->will($this->returnValue(true));

        $response = $this->controller->cloneAction(
            $card,
            $this->entityManager,
            $this->limitChecker,
            $this->boardLimitChecker
        );

        $this->assertEquals(
            new RedirectResponse('/card/kanban'),
            $response
        );
    }

    public function testCloneRedirectUserWithoutCardCloning()
    {
        $randomStatus = $this->statusFactory->createWithName('randomStatus' . rand(111, 999));

        $card = $this->cardFactory->createWithStatus($randomStatus);

        $this->entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->limitChecker = $this
            ->getMockBuilder('Kanban\Actors\LimitChecker')
            ->disableOriginalConstructor()
            ->getMock();

        $this->boardLimitChecker = $this
            ->getMockBuilder('Kanban\Actors\BoardLimitChecker')
            ->disableOriginalConstructor()
            ->getMock();

        $this->limitChecker->expects($this->once())
            ->method('setCard')
            ->with($card);

        $this->limitChecker->expects($this->once())
            ->method('setFutureStatusName')
            ->with($card->getStatus()->getName());

        $this->limitChecker->expects($this->once())
            ->method('isColumnLimitReached')
            ->will($this->returnValue(false));

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        /** I am not sire this si a good idea ... */
        $this->controller->setContainer($this->container);

        $this->boardLimitChecker->expects($this->once())
            ->method('isBoardLimitReached')
            ->will($this->returnValue(false));

        $response = $this->controller->cloneAction(
            $card,
            $this->entityManager,
            $this->limitChecker,
            $this->boardLimitChecker
        );

        $this->assertEquals(
            new RedirectResponse('/card/kanban'),
            $response
        );
    }
}

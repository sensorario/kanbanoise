<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;
use AppBundle\Responses\KResponse as ResponseBuilder;
use Doctrine\ORM\EntityManager;
use Kanban\Actors\BoardLimitChecker;
use Kanban\Actors\CardCounter;
use Kanban\Actors\CardMover;
use Kanban\Actors\DestinationFinder;
use Kanban\Actors\LimitChecker;
use Kanban\Actors\TagFinder;
use Kanban\Actors\WipChecker;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("card")
 */
class CardMovementsController extends Controller
{
    /**
     * @Route("/kanban/card/{card}/regress", name="card_regress")
     * @Method("GET")
     */
    public function regressAction(
        Card $card,
        \Kanban\Actors\DestinationFinder $finder
    ) {
        $finder->setCard($card);

        if ($finder->prevStatusFound()) {
            if ($finder->isColumnLimitReached()) {
                // @codeCoverageIgnoreStart
                $this->addFlash('notice', $finder->getLastErrorMessage());
                // @codeCoverageIgnoreEnd
            }
        }

        return new RedirectResponse('/card/kanban');
    }

    /**
     * @Route("/kanban/card/{card}/progress", name="card_progress")
     * @Method("GET")
     */
    public function progressAction(
        Card $card,
        \Kanban\Actors\DestinationFinder $finder
    ) {
        $finder->setCard($card);
        $finder->nextStatusFound();

        if ($finder->isColumnLimitReached()) {
                // @codeCoverageIgnoreStart
                $this->addFlash('notice', $finder->getLastErrorMessage());
                // @codeCoverageIgnoreEnd
        }

        return new RedirectResponse('/card/kanban');
    }

    /**
     * @Route("/{card}/move-to/{status}", name="card_move")
     * @Method({"GET", "POST"})
     */
    public function moveAction(
        Card $card,
        Status $status,
        \Kanban\Actors\CardMover $cardMover
    ) {
        $cardMover->setCard($card);
        $cardMover->setFutureStatusName($status);

        try {
            $cardMover->move();
        } catch(\RuntimeException $exception) {
            //$this->addFlash('notice', 'wip board limit reached');
            //$this->addFlash('notice', $exception->getMessage());

            return ResponseBuilder::createFailure();
        }

        return ResponseBuilder::createSuccess();
    }

    /**
     * @Route("/{id}", name="card_clone")
     * @Method("CLONE")
     */
    public function cloneAction(
        Card $card,
        EntityManager $manager,
        LimitChecker $columnChecker,
        BoardLimitChecker $boardChecker
    ) {
        $columnChecker->setCard($card);
        $columnChecker->setFutureStatusName($card->getStatus()->getName());

        if ($columnChecker->isColumnLimitReached($newCard = false, $cloned = true)) {
            // @codeCoverageIgnoreStart
            $this->addFlash('notice', 'wip column limit reached');
            // @codeCoverageIgnoreEnd
        }

        if ($boardChecker->isBoardLimitReached()) {
            // @codeCoverageIgnoreStart
            $this->addFlash('notice', 'wip board limit reached');
            // @codeCoverageIgnoreEnd
            return $this->redirectToRoute('kanban');
        }

        $newCard = clone $card;
        $manager->persist($newCard);
        $manager->flush();

        return new RedirectResponse('/card/kanban');
    }
}

<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;

class CardMover
{
    private $card;

    private $futureStatus;

    private $persistor;

    private $columnLimitChecker;

    private $reindexer;

    public function __construct(
        Persistor $persistor,
        LimitChecker $columnLimitChecker,
        Reindexer $reindexer
    ) {
        $this->persistor          = $persistor;
        $this->columnLimitChecker = $columnLimitChecker;
        $this->reindexer          = $reindexer;
    }

    public function setCard(Card $card)
    {
        $this->card = $card;
        $this->columnLimitChecker->setCard($this->card);
    }

    public function setFutureStatusName(Status $futureStatus)
    {
        $this->futureStatus = $futureStatus;
    }

    public function move()
    {
        $statusName = $this->futureStatus->getName();

        $this->columnLimitChecker->setFutureStatusName($statusName);

        if ($this->columnLimitChecker->isColumnLimitReached($newCard = false)) {
            throw new \RuntimeException(
                'Oops! Limit reached'
            );
        }

        $position = $this->columnLimitChecker->getHighestPositionNumber();

        $this->card->setStatus($this->futureStatus);
        $this->card->setPosition($position + 1);
        $this->persistor->save($this->card);

        $this->reindexer->reindexColumn($this->futureStatus);
    }
}

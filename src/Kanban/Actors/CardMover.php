<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;

class CardMover
{
    private $card;

    private $status;

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
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function move()
    {
        $this->columnLimitChecker->setFutureStatusName($this->status);

        if ($this->columnLimitChecker->isColumnLimitReached()) {
            throw new \RuntimeException(
                'Oops! Limit reached'
            );
        }

        $position = $this->columnLimitChecker->getHighestPositionNumber();

        $this->card->setStatus($this->status);
        $this->card->setPosition($position + 1);
        $this->persistor->save($this->card);

        /** @todo reindex cards in column */

        $this->reindexer->reindexColumn($this->status);
    }
}

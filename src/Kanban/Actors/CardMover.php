<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;

class CardMover
{
    private $card;

    private $status;

    private $persistor;

    private $columnLimitChecker;

    public function __construct(
        Persistor $persistor,
        LimitChecker $columnLimitChecker
    ) {
        $this->persistor = $persistor;
        $this->columnLimitChecker = $columnLimitChecker;
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

        $this->card->setStatus($this->status);
        $this->persistor->save($this->card);
    }
}

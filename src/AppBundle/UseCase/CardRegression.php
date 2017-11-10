<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;
use Kanban\Actors\Finder;
use Kanban\Actors\Persistor;

class CardRegression
{
    private $card;

    private $finder;

    private $persistor;

    public function __construct(
        Finder $finder,
        Persistor $persistor
    ) {
        $this->finder = $finder;
        $this->persistor = $persistor;

        $this->init();
    }

    private function init()
    {
        $this->finder->setEntity(Status::class);
    }

    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    public function execute()
    {
        $status = $this->finder->findByName($this->card->getStatus());

        if ($status->getPosition() == 1) {
            return true;
        }

        /** @todo ensure all position are numerical and sequentials */
        $position = $status->getPosition() - 1;
        $newStatus = $this->finder->findByPosition($position);

        $this->card->setStatus($newStatus);
        $this->persistor->save($this->card);

        return true;
    }
}

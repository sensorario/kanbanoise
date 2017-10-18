<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;

class CardRegression
{
    private $card;

    private $finder;

    private $persistor;

    public function __construct(
        \AppBundle\Actors\Finder $finder,
        \AppBundle\Actors\Persistor $persistor
    ) {
        $this->finder = $finder;
        $this->persistor = $persistor;
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

        $position = $status->getPosition() - 1;
        $newStatus = $this->finder->findByPosition($position);
        $this->card->setStatus($newStatus->getName());
        $this->persistor->save($this->card);

        return true;
    }
}

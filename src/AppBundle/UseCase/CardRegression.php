<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;

class CardRegression
{
    private $manager;

    private $card;

    private $finder;

    private $persistor;

    public function __construct(
        \Doctrine\ORM\EntityManager $manager,
        \AppBundle\Actors\Finder $finder,
        \AppBundle\Actors\Persistor $persistor
    ) {
        $this->manager = $manager;
        $this->finder = $finder;
        $this->persistor = $persistor;
    }

    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    public function execute()
    {
        $status = $this->finder->findByName(Status::class, $this->card->getStatus());

        if ($status->getPosition() == 1) {
            return true;
        }

        $position = $status->getPosition() - 1;
        $newStatus = $this->finder->findByPosition(Status::class, $position);
        $this->card->setStatus($newStatus->getName());
        $this->persistor->save($this->card);

        return true;
    }
}

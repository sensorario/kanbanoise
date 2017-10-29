<?php

namespace Kanban\Actors;

use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

class CardCounter
{
    private $entityManager;

    private $logger;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function numberOfCountableCards()
    {
        $allCards = $this->entityManager
            ->getRepository('AppBundle:Card')
            ->findCountable();

        return count($allCards);
    }
}

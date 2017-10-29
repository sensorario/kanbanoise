<?php

namespace Kanban\Actors;

use AppBundle\Entity\Board;
use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

class WipChecker
{
    private $entityManager;

    private $logger;

    private $futureCardStatus;

    private $card;

    public function __construct(
        EntityManager $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    public function getBoardWipLimit()
    {
        $boards = $this->entityManager
            ->getRepository(\AppBundle\Entity\Board::class)
            ->findAll();

        if (count($boards) == 0) {
            throw new \RuntimeException(
                'Oops! No boards found'
            );
        }

        $boardConf = $boards[0];

        return $boardConf->getWipLimit();
    }
}

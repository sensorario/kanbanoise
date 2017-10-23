<?php

namespace Kanban\Actors;

use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

class BoardLimitChecker
{
    private $entityManager;

    private $logger;

    public function __construct(
        EntityManager $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function isBoardLimitReached()
    {
        $boards = $this->entityManager->getRepository('AppBundle:Board')->findAll();

        if (count($boards) == 0) {
            throw new \RuntimeException(
                'Oops! No boards found'
            );
        }

        $boardConf = $boards[0];

        $cardInBoard = count($this->entityManager->getRepository('AppBundle:Card')->findAll());

        if ($cardInBoard >= $boardConf->getWipLimit()) {
            $this->logger->critical('threshold reached');

            return true;
        }

        return false;
    }
}

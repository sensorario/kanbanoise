<?php

namespace Kanban\Actors;

use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

class BoardLimitChecker
{
    private $entityManager;

    private $logger;

    private $wipChecker;

    private $counter;

    public function __construct(
        EntityManager $entityManager,
        LoggerInterface $logger,
        WipChecker $wipChecker,
        CardCounter $counter
    ) {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
        $this->wipChecker    = $wipChecker;
        $this->counter       = $counter;
    }

    public function isBoardLimitReached()
    {
        if ($this->counter->numberOfCountableCards() >= $this->wipChecker->getBoardWipLimit()) {
            $this->logger->critical('threshold reached');

            return true;
        }

        return false;
    }
}

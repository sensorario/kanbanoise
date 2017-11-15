<?php

namespace Kanban\Actors;

class DestinationFinder
{
    private $manager;

    private $columnLimitChecker; 
    private $lastErrorMessage;

    private $isColumnLimitReached;

    private $card;

    public function __construct(
        \Doctrine\ORM\EntityManager $manager,
        LimitChecker $columnLimitChecker,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->manager            = $manager;
        $this->columnLimitChecker = $columnLimitChecker;
        $this->logger             = $logger;
    }

    public function setCard(\AppBundle\Entity\Card $card)
    {
        $this->card = $card;
    }

    /** @todo rename previousStatusExists */
    public function prevStatusFound()
    {
        $this->ensureCardIsDefined();
        $statuses = $this->getAllStatuses();
        $previousVisitedStatus = null;
        $previousStatus = null;

        foreach ($statuses as $status) {
            if ($previousVisitedStatus == true && $status == $this->card->getStatus()) {
                if ($this->isStatusLimitReached($previousStatus)) {
                    $this->lastErrorMessage = 'wip column limit reached';
                    $this->isColumnLimitReached = true;
                    $this->logger->critical('<<< limit reached');
                    return true;
                }
                $this->updateCurrentCardWithStatus($previousStatus);
                $this->logger->critical('<<< limit not reached with update');
                return true;
            }
            $previousVisitedStatus = $status;
            $previousStatus = $status;
        }

        $this->logger->critical('<<< limit not reached');
        return false;
    }

    public function updateCurrentCardWithStatus(\AppBundle\Entity\Status $status)
    {
        $this->card->setStatus($status);
        $this->manager->persist($this->card);
        $this->manager->flush();
    }

    public function nextStatusFound()
    {
        $nextIsNext = false;

        foreach ($this->getAllStatuses() as $status) {
            if ($nextIsNext == true) {
                if ($this->isStatusLimitReached($status)) {
                    $this->lastErrorMessage = 'wip column limit reached';
                    $this->isColumnLimitReached = true;
                    $this->logger->critical('>>> limit reached');
                    return true;
                }
                $this->updateCurrentCardWithStatus($status);
                $this->logger->critical('>>> limit not reached with update');
                return true;
            }

            if ($status->getName() == $this->card->getStatus()) {
                $nextIsNext = true;
            }
        }

        $this->logger->critical('>>> limit not reached');
        return false;
    }

    public function isColumnLimitReached()
    {
        return true == $this->isColumnLimitReached;
    }

    public function getLastErrorMessage()
    {
        return $this->lastErrorMessage;
    }

    public function getAllStatuses()
    {
        return $this->manager
            ->getRepository('AppBundle:Status')
            ->findAll();
    }

    public function ensureCardIsDefined()
    {
        if (!$this->card) {
            throw new \RuntimeException(
                'Oops! Card is not defined'
            );
        }
    }

    public function isStatusLimitReached(\AppBundle\Entity\Status $status)
    {
        //$this->columnLimitChecker->setCard($this->card);

        $this->columnLimitChecker->setFutureStatusName($status);

        $this->logger->critical('status controlled : ' . $status);

        return $this->columnLimitChecker->isColumnLimitReached($newCard = false);
    }
}

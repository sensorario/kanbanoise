<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

/** @todo add ColumnLimitChecker */
class LimitChecker
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

    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    public function setFutureStatusName(string $futureCardStatus)
    {
        $this->futureCardStatus = $futureCardStatus;
    }

    /** @todo isColumnLimitReachedForNewCard */
    /** @todo isColumnLimitReachedForOldCard */
    /** @todo move this to a collaborator */
    public function isColumnLimitReached(bool $newCard)
    {
        $this->ensureCardIsDefined();

        if ($newCard) {
            $this->logger->critical(' new card ');

            $statusName = $this->card->getStatus()->getName();
            $status = $this->entityManager
                ->getRepository(\AppBundle\Entity\Status::class)
                ->findOneBy($criteria = [
                    'name' => $statusName
                ]);
            $columnLimit = $status->getWipLimit();
            if (null === $columnLimit) {
                return false;
            }

            $numberOfCardsInStatus = count($this->entityManager
                ->getRepository(\AppBundle\Entity\Card::class)
                ->findBy([
                    'status' => $status
                ]));

            $this->logger->critical(var_export([
                'numberOfCardsInStatus' => $numberOfCardsInStatus,
                'columnLimit' => $columnLimit,
            ], true));

            if ($numberOfCardsInStatus < $columnLimit) {
                return false;
            }
        }

        $oldCard = !$newCard;

        if ($oldCard) {
            $statusName = $this->futureCardStatus;
            $status = $this->entityManager
                ->getRepository(\AppBundle\Entity\Status::class)
                ->findOneBy($criteria = [
                    'name' => $statusName
                ]);
            $columnLimit = $status->getWipLimit();
            if (null === $columnLimit) {
                return false;
            }

            $numberOfCardsInStatus = count($this->entityManager
                ->getRepository(\AppBundle\Entity\Card::class)
                ->findBy([
                    'status' => $status
                ]));
            if ($numberOfCardsInStatus <= $columnLimit) {
                return false;
            }
        }

        return true;
    }

    public function getHighestPositionNumber()
    {
        if (!$this->futureCardStatus) {
            throw new \RuntimeException(
                'Oops! Future status is not defined'
            );
        }

        $status = $this->entityManager
            ->getRepository(\AppBundle\Entity\Status::class)
            ->findOneBy([
                'name' => $this->futureCardStatus,
            ]);

        $sql = 'select max(c.position) as position ' .
            'from AppBundle\Entity\Card c ' .
            'where c.status = \'' . $status->getId() . '\'';

        $res = $this->entityManager->createQuery($sql)->execute();

        $position = $res[0]['position'];

        $this->logger->critical(' position found : ' . (int) $position);

        return $position;
    }

    public function ensureFutureStatusIsDefined()
    {
        if (!$this->futureCardStatus) {
            throw new \RuntimeException(
                'Oops! Future status is not defined'
            );
        }
    }

    private function ensureCardIsDefined()
    {
        if (!$this->card) {
            throw new \RuntimeException(
                'Oops! Card is not defined'
            );
        }
    }
}

<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

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

    public function isColumnLimitReached()
    {
        if (null != $this->card) {
            $futureCardStatus = $this->card->getStatus();
        } elseif (null != $this->futureCardStatus) {
            $futureCardStatus = $this->futureCardStatus;
        } else {
            throw new \RuntimeException(
                'Oops! Status is not defined'
            );
        }

        $cardInStatus = $this->entityManager->getRepository('AppBundle:Card')
            ->findby(['status' => $futureCardStatus]);

        $numberOfCardInCurrentStatus = count($cardInStatus);

        $status = $this->entityManager->getRepository('AppBundle:Status')
            ->findOneBy(['name' => $futureCardStatus]);

        $statusEntity = $this->entityManager->getRepository('AppBundle:Status')
            ->findOneBy(['name' => $futureCardStatus]);

        $limitOfCard = $statusEntity->getWipLimit();

        if ($limitOfCard > 0 && $limitOfCard <= $numberOfCardInCurrentStatus) {
            $this->logger->critical('threshold reached');
            return true;
        }

        return false;
    }

    private function ensureCardIsDefined()
    {
        if (!$this->card) {
            throw new \RuntimeException(
                'Oops! Card is not defined'
            );
        }
    }

    public function getHighestPositionNumber()
    {
        if (!$this->futureCardStatus) {
            throw new \RuntimeException(
                'Oops! Future status is not defined'
            );
        }

        $sql = 'select max(c.position) as position ' .
            'from AppBundle\Entity\Card c ' .
            'where c.status = \'' . $this->futureCardStatus . '\'';

        $res = $this->entityManager->createQuery($sql)->execute();

        $position = $res[0]['position'];

        $this->logger->critical(' position found : ' . (int) $position);

        return $position;
    }
}

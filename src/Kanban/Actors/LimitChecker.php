<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Psr\log\LoggerInterface;

class LimitChecker
{
    private $entityManager;

    private $logger;

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

    public function isColumnLimitReached()
    {
        /** @todo ensure card is defined */

        $cardInStatus = $this->entityManager->getRepository('AppBundle:Card')
            ->findby(['status' => $this->card->getStatus()]);
        $numberOfCardInCurrentStatus = count($cardInStatus);

        $status = $this->entityManager->getRepository('AppBundle:Status')
            ->findOneBy(['name' => $this->card->getStatus()]);

        $status = $this->entityManager->getRepository('AppBundle:Status')
            ->findOneBy(['name' => $this->card->getStatus()]);

        $limitOfCard = $status->getWipLimit();

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
}

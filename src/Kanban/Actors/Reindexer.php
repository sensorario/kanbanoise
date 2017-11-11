<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;
use Doctrine\ORM\EntityManager;
Use Psr\Log\LoggerInterface;

class Reindexer
{
    private $manager;

    private $logger;

    public function __construct(
        EntityManager $manager,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function reindexColumn(Status $status)
    {
        $cards = $this->manager
            ->getRepository(Card::class)
            ->findBy([
                'status' => $status,
            ], [
                'position' => 'ASC',
            ]);

        foreach ($cards as $index => $card) {
            $card->setPosition($index + 1);
            $this->manager->persist($card);
            $this->manager->flush();
        }
    }
}

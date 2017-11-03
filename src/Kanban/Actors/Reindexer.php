<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

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

    public function reindexColumn(string $columnName)
    {
        $cards = $this->manager
            ->getRepository(Card::class)
            ->findBy([
                'status' => $columnName,
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

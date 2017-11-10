<?php

namespace Kanban\Factories;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;

class CardFactory
{
    public static function buildWithStatus(Status $status) : Card
    {
        $card = new Card();

        $card->setStatus($status);
        $card->setTitle($status);
        $card->setDescription($status);
        $card->setType('task');
        $card->setDatetime(new \DateTime('now'));

        return $card;
    }

    public function createWithStatus(Status $status)
    {
        return self::buildWithStatus($status);
    }
}

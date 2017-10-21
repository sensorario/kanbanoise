<?php

namespace Kanban\Factories;

use AppBundle\Entity\Card;

class CardFactory
{
    public static function buildWithStatus(string $status) : Card
    {
        $card = new Card();

        $card->setStatus($status);
        $card->setTitle($status);
        $card->setDescription($status);
        $card->setType('task');
        $card->setDatetime(new \DateTime('now'));

        return $card;
    }
}

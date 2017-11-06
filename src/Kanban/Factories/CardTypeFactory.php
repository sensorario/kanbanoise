<?php

namespace Kanban\Factories;

use AppBundle\Entity\CardType;

class CardTypeFactory
{
    public static function buildWithName(string $name) : CardType
    {
        $type = new CardType();

        $type->setName($name);

        return $type;
    }
}

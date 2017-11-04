<?php

namespace Kanban\Factories;

use AppBundle\Entity;

class BoardFactory
{
    public static function buildWithOwner($owner) : Entity\Board
    {
        $board = new Entity\Board();

        $board->setOwner($owner);
        $board->setWipLimit(42);

        return $board;
    }
}

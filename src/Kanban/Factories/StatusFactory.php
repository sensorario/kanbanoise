<?php

namespace Kanban\Factories;

use AppBundle\Entity\Card;

class StatusFactory
{
    public static function buildWithNameAndWipLimit($name, $wipLimit)
    {
        $status = new \AppBundle\Entity\Status();
        $status->setName($name);
        $status->setPosition(1);
        $status->setWipLimit($wipLimit);

        return $status;
    }

    public function createWithName(string $statusName)
    {
        return self::buildWithNameAndWipLimit($statusName, null);
    }
}

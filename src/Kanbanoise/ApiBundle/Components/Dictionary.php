<?php

namespace Kanbanoise\ApiBundle\Components;

final class Dictionary
{
    private $resourceName;

    private static $resourceToEntityClassMap = [
        'board' => \AppBundle\Entity\Board::class,
        'card' => \AppBundle\Entity\Card::class,
        'card-type' => \AppBundle\Entity\CardType::class,
        'status' => \AppBundle\Entity\Status::class,
        'tag' => \AppBundle\Entity\Tag::class,
    ];

    public function setResourceName(string $name)
    {
        $this->resourceName = $name;
    }

    public function knowResource()
    {
        return isset(self::$resourceToEntityClassMap[$this->resourceName]);
    }

    public function getEntityClassName()
    {
        return self::$resourceToEntityClassMap[$this->resourceName];
    }
}

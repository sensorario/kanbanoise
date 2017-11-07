<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Board implements \JsonSerializable
{
    private $id;

    private $owner;

    private $wipLimit;

    public function getId()
    {
        return $this->id;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setWipLimit($wipLimit)
    {
        $this->wipLimit = $wipLimit;

        return $this;
    }

    public function getWipLimit()
    {
        return $this->wipLimit;
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "owner" => $this->getOwner(),
            "wip_limit" => $this->getWipLimit(),
        ];
    }
}


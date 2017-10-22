<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Board
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
}


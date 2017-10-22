<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class CardType
{
    private $id;

    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}

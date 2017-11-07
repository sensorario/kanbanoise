<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class CardType implements \JsonSerializable
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

    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
        ];
    }
}

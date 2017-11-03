<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Card;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Tag
{
    private $id;

    private $name;

    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

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

    public function addCard(Card $card)
    {
        $this->cards[] = $card;
    }

    public function removeCard(Card $card)
    {
        $this->cards->removeElement($card);
    }
}


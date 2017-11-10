<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Card;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Status
{
    private $id;

    private $name;

    private $position;

    private $wipLimit;

    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function addCard(Card $card)
    {
        $this->cards[] = $card;
    }

    public function removeCard(Card $card)
    {
        $this->cards->removeElement($card);
    }

    public function getCards()
    {
        return $this->cards;
    }

    public static function fromArray(array $params)
    {
        $obj = new self();
        $obj->setName($params['name']);
        $obj->setPosition($params['position']);
        return $obj;
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

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
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

    public function __toString()
    {
        return $this->getName();
    }
}


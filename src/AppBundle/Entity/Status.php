<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Status
{
    private $id;

    private $name;

    private $position;

    private $wipLimit;

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
}


<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Status;
use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Card
{
    private $id;

    private $title;

    private $description;

    private $status;

    private $member;

    private $owner;

    private $type;

    private $datetime;

    private $expiration;

    private $project;

    private $createdOn;

    private $tags;

    private $position;

    public function __construct()
    {
        $this->expiration = new \DateTime('+1 week');
        $this->tags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setMember($name)
    {
        $this->member = $name;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setProject($project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function addTag(Tag $tag)
    {
        $tag->addCard($this);
        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
        $tag->removeCard($this);
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
}


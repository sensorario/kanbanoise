<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class StoredDatabase
{
    private $id;

    private $relativePath;

    private $originalName;

    public function getId()
    {
        return $this->id;
    }

    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;

        return $this;
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }
}


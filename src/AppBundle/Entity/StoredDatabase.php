<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stored_database")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StoredDatabaseRepository")
 */
class StoredDatabase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="relative_path", type="string", length=255)
     */
    private $relativePath;

    /**
     * @ORM\Column(name="original_name", type="string", length=255)
     */
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


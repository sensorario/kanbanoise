<?php

namespace AppBundle\Actors;

class Finder
{
    private $manager;

    private $entityClass;

    public function __construct(\Doctrine\ORM\EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function setEntity(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function entureEntityClasssIsDefined()
    {
        if (!$this->entityClass) {
            throw new \RuntimeException(
                'Oops! Entity class is not defined'
            );
        }
    }

    public function findByPosition(string $position)
    {
        $this->entureEntityClasssIsDefined();

        return $this->manager
            ->getRepository($this->entityClass)
            ->findOneBy([
                'position' => $position,
            ]);
    }

    public function findByName(string $name)
    {
        $this->entureEntityClasssIsDefined();

        return $this->manager
            ->getRepository($this->entityClass)
            ->findOneBy([
                'name' => $name,
            ]);
    }
}

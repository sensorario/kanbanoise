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

    public function ensureEntityClasssIsDefined()
    {
        if (!$this->entityClass) {
            throw new \RuntimeException(
                'Oops! Entity class is not defined'
            );
        }
    }

    public function findByPosition(string $position)
    {
        $this->ensureEntityClasssIsDefined();

        return $this->manager
            ->getRepository($this->entityClass)
            ->findOneBy([
                'position' => $position,
            ]);
    }

    public function findByName(string $name)
    {
        $this->ensureEntityClasssIsDefined();

        return $this->manager
            ->getRepository($this->entityClass)
            ->findOneBy([
                'name' => $name,
            ]);
    }

    public function findBy(array $criteria)
    {
        $this->ensureEntityClasssIsDefined();

        return $this->manager
            ->getRepository($this->entityClass)
            ->findOneBy($criteria);
    }
}

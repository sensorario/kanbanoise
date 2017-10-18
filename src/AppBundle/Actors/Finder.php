<?php

namespace AppBundle\Actors;

class Finder
{
    private $manager;

    public function __construct(\Doctrine\ORM\EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function findByPosition(string $entityClass, string $position)
    {
        return $this->manager
            ->getRepository($entityClass)
            ->findOneBy([
                'position' => $position,
            ]);
    }

    public function findByName(string $entityClass, string $name)
    {
        return $this->manager
            ->getRepository($entityClass)
            ->findOneBy([
                'name' => $name,
            ]);
    }
}

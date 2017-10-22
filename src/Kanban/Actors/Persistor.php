<?php

namespace Kanban\Actors;

class Persistor
{
    private $manager;

    public function __construct(\Doctrine\ORM\EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function save($entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}

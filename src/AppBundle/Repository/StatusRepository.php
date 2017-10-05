<?php

namespace AppBundle\Repository;

class StatusRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy([], [
            'position' => 'ASC',
        ]);
    }
}

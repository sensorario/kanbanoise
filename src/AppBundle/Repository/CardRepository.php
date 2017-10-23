<?php

namespace AppBundle\Repository;

class CardRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy([], [
            'status' => 'ASC',
        ]);
    }

    public function findCountable()
    {
        $qb = $this->createQueryBuilder('c');

        $qb->where('c.status != :status')
            ->setParameter('status', 'todo');

        return $qb->getQuery()
            ->getResult();
    }
}

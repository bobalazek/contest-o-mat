<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class WinnerRepository extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class VoteRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

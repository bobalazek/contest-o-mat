<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class VoteMetaRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('vm')
            ->select('COUNT(vm.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

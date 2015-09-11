<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class EntryRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

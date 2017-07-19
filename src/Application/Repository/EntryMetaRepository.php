<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class EntryMetaRepository extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('em')
            ->select('COUNT(em.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

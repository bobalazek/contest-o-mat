<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ParticipantMetaRepository extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('pm')
            ->select('COUNT(pm.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

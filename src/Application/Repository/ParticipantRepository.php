<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ParticipantRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

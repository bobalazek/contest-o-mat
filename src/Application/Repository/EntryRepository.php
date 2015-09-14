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

    /**
     * To-Do: This function may need a little refactoring.
     *
     * @return array
     */
    public function getByHours()
    {
        $results = array();

        $databaseResults = $this->createQueryBuilder('e')
            ->select("e.timeCreated AS date")
            ->orderBy('e.timeCreated', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $firstDate = date('Y-m-d H:i:s', strtotime('-2 days'));
        $lastDate = date('Y-m-d H:i:s');
        $dates = dateRange($firstDate, $lastDate, '+ 1 hour', 'Y-m-d H');

        foreach ($dates as $date) {
            $count = 0;

            if ($databaseResults) {
                foreach ($databaseResults as $databaseResult) {
                    if (strpos($date, $databaseResult['date']->format('Y-m-d H')) !== false) {
                        $count++;
                    }
                }
            }

            $results[] = array(
                'date' => $date.':00',
                'count' => $count,
            );
        }

        return $results;
    }
}

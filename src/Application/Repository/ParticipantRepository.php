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

    /**
     * @return array
     */
    public function getByHours()
    {
        $results = array();

        $databaseResults = $this->createQueryBuilder('p')
            ->select('p.timeCreated AS date')
            ->orderBy('p.timeCreated', 'DESC')
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

    /**
     * @return array
     */
    public function getByDays()
    {
        $results = array();

        $databaseResults = $this->createQueryBuilder('p')
            ->select('p.timeCreated AS date')
            ->orderBy('p.timeCreated', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $firstDate = date('Y-m-d H:i:s', strtotime('-4 weeks'));
        $lastDate = date('Y-m-d H:i:s');
        $dates = dateRange($firstDate, $lastDate, '+ 1 day', 'Y-m-d');

        foreach ($dates as $date) {
            $count = 0;

            if ($databaseResults) {
                foreach ($databaseResults as $databaseResult) {
                    if (strpos($date, $databaseResult['date']->format('Y-m-d')) !== false) {
                        $count++;
                    }
                }
            }

            $results[] = array(
                'date' => $date,
                'count' => $count,
            );
        }

        return $results;
    }

    /**
     * @return array
     */
    public function getByBrowsers($app)
    {
        $data = array(
            'Chrome' => 0,
            'Firefox' => 0,
            'Opera' => 0,
            'Safari' => 0,
            'IE' => 0,
        );

        $participants = $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult()
        ;

        if ($participants) {
            foreach ($participants as $participant) {
                $uaParserInfo = $app['ua.parser']->parse($participant->getUserAgent());
                $browser = $uaParserInfo->ua->family;

                if (! isset($data[$browser])) {
                    $data[$browser] = 0;
                } else {
                    $data[$browser]++;
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByOperatingSystems($app)
    {
        $data = array(
            'Windows' => 0,
            'Mac OS X' => 0,
            'Linux' => 0,
        );

        $participants = $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult()
        ;

        if ($participants) {
            foreach ($participants as $participant) {
                $uaParserInfo = $app['ua.parser']->parse($participant->getUserAgent());
                $os = $uaParserInfo->os->family;

                if (! isset($data[$os])) {
                    $data[$os] = 0;
                } else {
                    $data[$os]++;
                }
            }
        }

        return $data;
    }
}

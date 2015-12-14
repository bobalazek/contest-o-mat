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

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    TIMESTAMP(CONCAT(
                        DATE(p.timeCreated),
                        ' ',
                        HOUR(p.timeCreated),
                        ':00:00'
                    )) AS date,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY date
                ORDER BY date DESC"
            )
            ->getArrayResult()
        ;

        $firstDate = date('Y-m-d H:00:00', strtotime('-2 days'));
        $lastDate = date('Y-m-d H:00:00');
        $dates = dateRange($firstDate, $lastDate, '+ 1 hour', 'Y-m-d H:00:00');

        foreach ($dates as $date) {
            $count = 0;

            if ($databaseResults) {
                foreach ($databaseResults as $databaseResult) {
                    if ($databaseResult['date'] == $date) {
                        $count = (int) $databaseResult['countNumber'];
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
    public function getByDays()
    {
        $results = array();

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    DATE(p.timeCreated) AS date,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY date
                ORDER BY date DESC"
            )
            ->getArrayResult()
        ;

        $firstDate = date('Y-m-d', strtotime('-4 weeks'));
        $lastDate = date('Y-m-d');
        $dates = dateRange($firstDate, $lastDate, '+ 1 day', 'Y-m-d');

        foreach ($dates as $date) {
            $count = 0;

            if ($databaseResults) {
                foreach ($databaseResults as $databaseResult) {
                    if ($databaseResult['date'] == $date) {
                        $count = (int) $databaseResult['countNumber'];
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

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.userAgentUa,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.userAgentUa"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $userAgentUa = $databaseResult['userAgentUa'];

                $data[$userAgentUa] = $databaseResult['countNumber'];
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
            'Windows 7' => 0,
            'Windows XP' => 0,
            'Mac OS X' => 0,
            'Linux' => 0,
        );

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.userAgentOs,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.userAgentOs"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $userAgentOs = $databaseResult['userAgentOs'];

                $data[$userAgentOs] = $databaseResult['countNumber'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByDeviceTypes($app)
    {
        $data = array(
            'Desktop' => 0,
            'Tablet' => 0,
            'Mobile' => 0,
        );

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.userAgentDeviceType,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.userAgentDeviceType"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $userAgentDeviceType = $databaseResult['userAgentDeviceType'];

                $data[$userAgentDeviceType] = $databaseResult['countNumber'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByDevices($app)
    {
        $data = array(
            'Other' => 0,
            'Android' => 0,
            'Apple' => 0,
        );

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.userAgentDevice,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.userAgentDevice"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $userAgentDevice = $databaseResult['userAgentDevice'];

                $data[$userAgentDevice] = $databaseResult['countNumber'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByCities($app)
    {
        $data = array(
            'Unknown' => 0,
        );

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.ipCity,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.ipCity"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $ipCity = $databaseResult['ipCity'];

                $data[$ipCity] = $databaseResult['countNumber'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByCountries($app)
    {
        $data = array(
            'Unknown' => 0,
        );

        $databaseResults = $this->getEntityManager()
            ->createQuery(
                "SELECT
                    p.ipCountry,
                    COUNT(p.id) AS countNumber
                FROM Application\Entity\ParticipantEntity p
                GROUP BY p.ipCountry"
            )
            ->getArrayResult()
        ;

        if ($databaseResults) {
            foreach ($databaseResults as $databaseResult) {
                $ipCountry = $databaseResult['ipCountry'];

                $data[$ipCountry] = $databaseResult['countNumber'];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getByQuery($query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id LIKE :query')
            ->orWhere('p.name LIKE :query')
            ->orWhere('p.email LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}

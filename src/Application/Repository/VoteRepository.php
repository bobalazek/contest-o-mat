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
                        DATE(v.timeCreated),
                        ' ',
                        HOUR(v.timeCreated),
                        ':00:00'
                    )) AS date,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
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
                    DATE(v.timeCreated) AS date,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
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
                    v.userAgentUa,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.userAgentUa"
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
                    v.userAgentOs,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.userAgentOs"
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
                    v.userAgentDeviceType,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.userAgentDeviceType"
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
                    v.userAgentDevice,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.userAgentDevice"
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
                    v.ipCity,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.ipCity"
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
                    v.ipCountry,
                    COUNT(v.id) AS countNumber
                FROM Application\Entity\VoteEntity v
                GROUP BY v.ipCountry"
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
}

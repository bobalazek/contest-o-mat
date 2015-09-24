<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participant Entity
 *
 * @ORM\Table(name="participants")
 * @ORM\Entity(repositoryClass="Application\Repository\ParticipantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ParticipantEntity
{
    /*************** Variables ***************/
    /********** General Variables **********/
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Useful if you are building an application with external API,
     * so you can save the user's ID like facebook:123456, twitter:123456, ...
     *
     * @var string
     *
     * @ORM\Column(name="uid", type="string", length=255, nullable=true)
     */
    protected $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="via", type="string", length=16)
     */
    protected $via;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    protected $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_continent", type="string", length=16, nullable=true)
     */
    protected $ipContinent = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_country", type="string", length=32, nullable=true)
     */
    protected $ipCountry = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_state", type="string", length=32, nullable=true)
     */
    protected $ipState = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_region", type="string", length=64, nullable=true)
     */
    protected $ipRegion = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_city", type="string", length=64, nullable=true)
     */
    protected $ipCity = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_latitude", type="string", length=32, nullable=true)
     */
    protected $ipLatitude = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_longitude", type="string", length=32, nullable=true)
     */
    protected $ipLongitude = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="text", nullable=true)
     */
    protected $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent_ua", type="string", length=32, nullable=true)
     */
    protected $userAgentUa;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent_os", type="string", length=32, nullable=true)
     */
    protected $userAgentOs;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent_device", type="string", length=32, nullable=true)
     */
    protected $userAgentDevice;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent_device_type", type="string", length=32, nullable=true)
     */
    protected $userAgentDeviceType = 'Desktop';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_created", type="datetime")
     */
    protected $timeCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_updated", type="datetime")
     */
    protected $timeUpdated;

    /***** Relationship Variables *****/
    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\EntryEntity", mappedBy="participant", cascade={"all"})
     */
    protected $entries;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\ParticipantMetaEntity", mappedBy="participant", cascade={"all"})
     */
    protected $participantMetas;

    protected $metas;

    /*************** Methods ***************/
    /********** Contructor **********/
    public function __construct()
    {
        $this->participantMetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /********** General Methods **********/
    /***** Getters, Setters and Other stuff *****/
    /*** Id ***/
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Uid ***/
    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /*** Name ***/
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /*** Email ***/
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /*** Via ***/
    public function getVia()
    {
        return $this->via;
    }

    public function setVia($via)
    {
        $this->via = $via;

        return $this;
    }

    /*** IP ***/
    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param $ip
     *
     * @return \Application\Entity\ParticipantEntity
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        // We shall not do requests from the command line
        // (prevent batch requests when hydrating test data)
        if (php_sapi_name() !== 'cli') {
            try {
                $ipData = json_decode(
                    @file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip)
                );
                $converterArray = array(
                    'ipContinent' => 'geoplugin_continentCode',
                    'ipCountry' => 'geoplugin_countryCode',
                    'ipState' => 'geoplugin_state',
                    'ipRegion' => 'geoplugin_region',
                    'ipCity' => 'geoplugin_city',
                    'ipLatitude' => 'geoplugin_latitude',
                    'ipLongitude' => 'geoplugin_longitude',
                );

                foreach ($converterArray as $key => $val) {
                    if (isset($ipData->{$val}) && $ipData->{$val} != '') {
                        $this->{$key} = $ipData->{$val};
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getIpContinent()
    {
        return $this->ipContinent;
    }

    /**
     * @return string
     */
    public function getIpCountry()
    {
        return $this->ipCountry;
    }

    /**
     * @return string
     */
    public function getIpState()
    {
        return $this->ipState;
    }

    /**
     * @return string
     */
    public function getIpRegion()
    {
        return $this->ipRegion;
    }

    /**
     * @return string
     */
    public function getIpCity()
    {
        return $this->ipCity;
    }

    /**
     * @return string
     */
    public function getIpLatitude()
    {
        return $this->ipLatitude;
    }

    /**
     * @return string
     */
    public function getIpLongitude()
    {
        return $this->ipLongitude;
    }

    /*** User agent ***/
    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param $userAgent
     *
     * @return \Application\Entity\ParticipantEntity
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        $parser = \UAParser\Parser::create();
        $detect = new \Mobile_Detect();
        $userAgentInfo = $parser->parse($userAgent);
        $userAgentMobileInfo = $detect->setUserAgent($userAgent);

        $this->userAgentUa = $userAgentInfo->ua->family;
        $this->userAgentOs = $userAgentInfo->os->family;
        $this->userAgentDevice = $userAgentInfo->device->family;

        if ($detect->isMobile()) {
            $this->userAgentDeviceType = 'Mobile';
        } elseif ($detect->isTablet()) {
            $this->userAgentDeviceType = 'Tablet';
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgentUa()
    {
        return $this->userAgentUa;
    }

    /**
     * @return string
     */
    public function getUserAgentOs()
    {
        return $this->userAgentOs;
    }

    /**
     * @return string
     */
    public function getUserAgentDevice()
    {
        return $this->userAgentDevice;
    }

    /**
     * @return string
     */
    public function getUserAgentDeviceType()
    {
        return $this->userAgentDeviceType;
    }

    /*** Time created ***/
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    public function setTimeCreated(\DateTime $timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /*** Time updated ***/
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /*** Entries ***/
    public function getEntries()
    {
        return $this->entries;
    }

    public function setEntries($entries)
    {
        $this->entries = $entries;

        return $this;
    }

    public function getLastEntry()
    {
        $entries = $this->getEntries()->toArray();
        $lastEntryIndex = count($entries) - 1;

        return is_array($entries) && count($entries)
            ? $entries[$lastEntryIndex]
            : false
        ;
    }

    public function hasAlreadyParticipatedToday()
    {
        $lastEntry = $this->getLastEntry();

        if ($lastEntry) {
            $currentTime = new \Datetime();
            $entryTime = $lastEntry->getTimeCreated();

            if ($currentTime->format('Y-m-d') == $entryTime->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    /*** Particiant Metas ***/
    public function getParticipantMetas()
    {
        return $this->participantMetas;
    }

    public function setParticipantMetas($participantMetas)
    {
        if ($participantMetas) {
            foreach ($participantMetas as $participantMeta) {
                $participantMeta->setParticipant($this);
            }

            $this->participantMetas = $participantMetas;
        }

        return $this;
    }

    public function addParticipantMeta(\Application\Entity\ParticipantMetaEntity $participantMeta)
    {
        if (! $this->participantMetas->contains($participantMeta)) {
            $participantMeta->setParticipant($this);

            $this->participantMetas->add($participantMeta);
        }

        return $this;
    }

    public function removeParticipantMeta(\Application\Entity\ParticipantMetaEntity $participantMeta)
    {
        $participantMeta->setParticipant(null);
        $this->participantMetas->removeElement($participantMeta);

        return $this;
    }

    /*** Metas ***/
    public function getMetas($key = null)
    {
        return $key
            ? (isset($this->metas[$key])
                ? $this->metas[$key]
                : null)
            : $this->metas
        ;
    }

    public function setMetas($metas)
    {
        $this->metas = $metas;

        return $this;
    }

    public function addMeta($key, $value = null)
    {
        $this->metas[$key] = is_string($value)
            ? $value
            : json_encode($value)
        ;

        return $this;
    }

    public function hydrateParticipantMetas()
    {
        $participantMetas = $this->getParticipantMetas()->toArray();

        if (count($participantMetas)) {
            $metas = array();

            foreach ($participantMetas as $participantMeta) {
                $metas[$participantMeta->getKey()] = $participantMeta->getValue();
            }

            $this->setMetas($metas);
        }
    }

    public function convertMetasToEntryMetas($uploadPath, $uploadDir)
    {
        $slugify = new \Cocur\Slugify\Slugify();
        $metas = $this->getMetas();

        if (! empty($metas)) {
            foreach ($metas as $metaKey => $metaValue) {
                $metaEntity = new \Application\Entity\ParticipantMetaEntity();

                // Chek if it's a file!
                if($metaValue instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $filename = $slugify->slugify(
                        $metaValue->getClientOriginalName()
                    );

                    $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
                        $metaValue->guessExtension()
                    ;

                    $metaValue->move(
                        $uploadDir,
                        $filename
                    );

                    $metaValue = $uploadPath.$filename;
                }

                $metaEntity
                    ->setKey($metaKey)
                    ->setValue($metaValue)
                ;

                $this
                    ->addParticipantMeta($metaEntity)
                ;
            }
        }
    }

    /********** API ***********/
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'metas' => $this->getMetas(),
            'time_created' => $this->getTimeCreated(),
        );
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getName().' <'.$this->getEmail().'>';
    }

    /********** Callback Methods **********/
    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->hydrateParticipantMetas();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setTimeUpdated(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setTimeUpdated(
            $this->timeUpdated
                ? $this->timeUpdated
                : new \DateTime('now')
        );
        $this->setTimeCreated(
            $this->timeUpdated
                ? $this->timeUpdated
                : new \DateTime('now')
        );
    }
}

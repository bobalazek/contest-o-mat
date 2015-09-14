<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participant Entity
 *
 * @ORM\Table(name="entries")
 * @ORM\Entity(repositoryClass="Application\Repository\EntryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EntryEntity
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
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    protected $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="text", nullable=true)
     */
    protected $userAgent;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\ParticipantEntity", inversedBy="entries")
     */
    protected $participant;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\EntryMetaEntity", mappedBy="entry", cascade={"all"})
     */
    protected $entryMetas;

    protected $metas;

    /*************** Methods ***************/
    /********** Contructor **********/
    public function __construct()
    {
        $this->entryMetas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return \Application\Entity\EntryEntity
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
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
     * @return \Application\Entity\EntryEntity
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
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

    /*** Participant ***/
    public function getParticipant()
    {
        return $this->participant;
    }

    public function setParticipant(\Application\Entity\ParticipantEntity $participant)
    {
        $this->participant = $participant;

        return $this;
    }

    /*** Entry Metas ***/
    public function getEntryMetas()
    {
        return $this->entryMetas;
    }

    public function setEntryMetas($entryMetas)
    {
        if ($entryMetas) {
            foreach ($entryMetas as $entryMeta) {
                $entryMeta->setEntry($this);
            }

            $this->entryMetas = $entryMetas;
        }

        return $this;
    }

    public function addEntryMeta(\Application\Entity\EntryMetaEntity $entryMeta)
    {
        if (! $this->entryMetas->contains($entryMeta)) {
            $entryMeta->setEntry($this);

            $this->entryMetas->add($entryMeta);
        }

        return $this;
    }

    public function removeEntryMeta(\Application\Entity\EntryMetaEntity $entryMeta)
    {
        $entryMeta->setEntry(null);
        $this->entryMetas->removeElement($entryMeta);

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

    public function hydrateEntryMetas()
    {
        $entryMetas = $this->getEntryMetas()->toArray();

        if (count($entryMetas)) {
            $metas = array();

            foreach ($entryMetas as $entryMeta) {
                $metas[$entryMeta->getKey()] = $entryMeta->getValue();
            }

            $this->setMetas($metas);
        }
    }

    /********** API ***********/
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'metas' => $this->getMetas(),
            'time_created' => $this->getTimeCreated(),
        );
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return 'Entry #'.$this->getId();
    }

    /********** Callback Methods **********/
    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->hydrateEntryMetas();
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
        $this->setTimeUpdated(new \DateTime('now'));
        $this->setTimeCreated(new \DateTime('now'));
    }
}

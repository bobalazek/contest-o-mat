<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Winner Entity.
 *
 * @ORM\Table(name="winners")
 * @ORM\Entity(repositoryClass="Application\Repository\WinnerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WinnerEntity
{
    /*************** Variables ***************/
    /********** General Variables **********/
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="integer", nullable=true)
     */
    protected $place;

    /**
     * @var string
     *
     * @ORM\Column(name="prize", type="text", nullable=true)
     */
    protected $prize;

    /**
     * @var bool
     *
     * @ORM\Column(name="informed", type="boolean")
     */
    protected $informed = false;

    /**
     * @var string
     *
     * @ORM\Column(name="informed_email", type="text", nullable=true)
     */
    protected $informedEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="informed_email_token", type="string", length=255, nullable=true)
     */
    protected $informedEmailToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_informed", type="datetime", nullable=true)
     */
    protected $timeInformed;

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
     * @ORM\OneToOne(targetEntity="Application\Entity\EntryEntity")
     */
    protected $entry;

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\ParticipantEntity")
     */
    protected $participant;

    /*************** Methods ***************/
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

    /*** Place ***/
    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /*** Prize ***/
    public function getPrize()
    {
        return $this->prize;
    }

    public function setPrize($prize)
    {
        $this->prize = $prize;

        return $this;
    }

    /*** Informed ***/
    public function getInformed()
    {
        return $this->informed;
    }

    public function isInformed()
    {
        return $this->getInformed();
    }

    public function setInformed($informed)
    {
        $this->informed = $informed;

        return $this;
    }

    public function inform()
    {
        $this->setInformed(true);

        $this->setTimeInformed(new \Datetime());

        return $this;
    }

    /*** Informed email ***/
    public function getInformedEmail()
    {
        return $this->informedEmail;
    }

    public function setInformedEmail($informedEmail)
    {
        $this->informedEmail = $informedEmail;

        return $this;
    }

    /*** Informed email token ***/
    public function getInformedEmailToken()
    {
        return $this->informedEmailToken;
    }

    public function setInformedEmailToken($informedEmailToken)
    {
        $this->informedEmailToken = $informedEmailToken;

        return $this;
    }

    /*** Time informed ***/
    public function getTimeInformed()
    {
        return $this->timeInformed;
    }

    public function setTimeInformed(\DateTime $timeInformed = null)
    {
        $this->timeInformed = $timeInformed;

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

    /*** Entry ***/
    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry(\Application\Entity\EntryEntity $entry = null)
    {
        $this->entry = $entry;

        return $this;
    }

    /*** Participant ***/
    public function getParticipant()
    {
        return $this->participant;
    }

    public function setParticipant(\Application\Entity\ParticipantEntity $participant = null)
    {
        $this->participant = $participant;

        return $this;
    }

    /********** API ***********/
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'participant' => $this->getParticipant(),
            'entry' => $this->getEntry(),
            'time_created' => $this->getTimeCreated(),
        );
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return 'Winner #'.$this->getId();
    }

    /********** Callback Methods **********/

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

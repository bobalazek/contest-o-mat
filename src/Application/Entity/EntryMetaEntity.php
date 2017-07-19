<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entry Entity.
 *
 * @ORM\Table(name="entry_metas")
 * @ORM\Entity(repositoryClass="Application\Repository\EntryMetaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EntryMetaEntity
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
     * @ORM\Column(name="`key`", type="string", length=255)
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(name="`value`", type="text")
     */
    protected $value;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\EntryEntity", inversedBy="entryMetas")
     */
    protected $entry;

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

    /*** Key ***/
    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /*** Value ***/
    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

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

    public function setEntry($entry)
    {
        $this->entry = $entry;

        return $this;
    }

    /********** API ***********/
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'time_created' => $this->getTimeCreated(),
        ];
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        $key = $this->getKey();
        $value = $this->getValue();

        // Prevent double encoding
        if ($value[0] == '{' || $value[0] == '[') {
            $value = json_decode($value);
        }

        $data[$key] = $value;

        return json_encode($data);
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
        $this->setTimeUpdated(new \DateTime('now'));
        $this->setTimeCreated(new \DateTime('now'));
    }
}

<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="users_events")
 * @ORM\Entity(repositoryClass="App\Repository\UserEventRepository")
 */
class UserEvent 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $participant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="participants")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")
     */
    private $event;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $confirmed;
      
    public function __construct()
    {
        $this->date = new \DateTime($this->date, new \DateTimeZone('Europe/Paris'));
        $this->confirmed = 0;
    }
     
    public function getId()
    {
        return $this->id;
    }
 
    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    
    public function setParticipant(?User $participant): self
    {
        $this->participant = $participant;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self 
    {
        $this->event = $event;
        return $this;
    }
 
    public function getDate()
    {
        return $this->date;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($confirmed) 
    {
        $this->confirmed = $confirmed;
        return $this;
    }
 
}
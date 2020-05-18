<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
 
/**
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
 
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(min=5, max=50, minMessage="Nom trop court.", maxMessage="Nom trop long.")
     * @Assert\Regex("/^([a-zA-Z0-9àáâäçéèêëîïôùûüÿÀÂÄÇÉÈÊËÎÏÔÙÛÜŸ \-\':.]+)$/Ui", message="Le nom contient des caractères interdits.")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(max=500, maxMessage="Description trop longue.")
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(max=100, maxMessage="Adresse trop longue.")
     */
    private $address;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $map;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(max=30, maxMessage="Coordonnée trop longue. (30 caractères max)")
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(max=30, maxMessage="Coordonnée trop longue. (30 caractères max)")
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserEvent", mappedBy="event")
     */
    private $participants;
      
    public function __construct()
    {
        $this->dateCreated = new \DateTime($this->dateCreated, new \DateTimeZone('Europe/Paris'));
        $this->participants = new ArrayCollection();
        $this->map = 0;
    }

    /**
     * @return Collection|UserEvent[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }
     
    /*
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }
 
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
 
    public function getName()
    {
        return $this->name;
    }
 
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
 
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
 
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }
 
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        $this->map = $map;
        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }
 
}
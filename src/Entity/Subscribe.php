<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="subscribes")
 * @ORM\Entity(repositoryClass="App\Repository\SubscribeRepository")
 */
class Subscribe 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscribers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", onDelete="cascade")
     */
    private $subscription;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;
      
    public function __construct()
    {
        $this->date = new \DateTime($this->date, new \DateTimeZone('Europe/Paris'));
    }
     
    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSubscription(): ?User
    {
        return $this->subscription;
    }

    public function setSubscription(?User $subscription): self
    {
        $this->subscription = $subscription;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }
 
}
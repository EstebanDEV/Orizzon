<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="req_users_privates")
 * @ORM\Entity(repositoryClass="App\Repository\ReqUserPrivateRepository")
 */
class ReqUserPrivate 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sendersPrv")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="targetsPrv")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", onDelete="cascade")
     */
    private $userTarget;

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

    public function getUserTarget(): ?User
    {
        return $this->userTarget;
    }

    public function setUserTarget(?User $userTarget): self
    {
        $this->userTarget = $userTarget;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }
 
}
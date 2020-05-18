<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sendersMsg")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="targetsMsg")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", onDelete="cascade")
     */
    private $userReceiveMsg;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;
      
    public function __construct()
    {
        $this->date = new \DateTime();
    }
     
    public function getId()
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUserReceiveMsg(): ?User
    {
        return $this->userReceiveMsg;
    }

    public function setUserReceiveMsg(?User $userReceiveMsg): self
    {
        $this->userReceiveMsg = $userReceiveMsg;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }
 
}
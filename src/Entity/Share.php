<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="shares")
 * @ORM\Entity(repositoryClass="App\Repository\ShareRepository")
 */
class Share 
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
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="shares")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="cascade")
     */
    private $post;
      
    public function __construct()
    {
   
    }
     
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self 
    {
        $this->post = $post;
        return $this;
    }
 
}
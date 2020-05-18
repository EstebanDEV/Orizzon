<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 */
class Like 
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="likes")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="cascade")
     */
    private $post;

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
 
    public function getDate()
    {
        return $this->date;
    }
 
}
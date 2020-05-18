<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
 
/**
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="cascade")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Share")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    private $shared;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;
 
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $lastModification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="post")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Share", mappedBy="post")
     */
    private $shares;
      
    public function __construct()
    {
        $this->date = new \DateTime($this->date, new \DateTimeZone('Europe/Paris'));
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->shares = new ArrayCollection();
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @return Collection|Share[]
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }
     
    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    public function getShared()
    {
        return $this->shared;
    }

    public function setShared($shared)
    {
        $this->shared = $shared;
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

    public function getContent()
    {
        return $this->content;
    }
 
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
 
    public function getNbLikes()
    {
        return $this->nbLikes;
    }
 
    public function setNbLikes($nbLikes)
    {
        $this->nbLikes = $nbLikes;
        return $this;
    }

    public function getNbComments()
    {
        return $this->nbComments;
    }
 
    public function setNbComments($nbComments)
    {
        $this->nbComments = $nbComments;
    }
 
    public function getDate()
    {
        return $this->date;
    }

    public function getLastModification()
    {
        return $this->lastModification;
    }
 
    public function setLastModification($lastModification)
    {
        $this->lastModification = $lastModification;
    }
 
}
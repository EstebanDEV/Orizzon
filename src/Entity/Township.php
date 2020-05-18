<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 
/**
 * @ORM\Table(name="townships")
 * @ORM\Entity(repositoryClass="App\Repository\TownshipRepository")
 */
class Township 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="text")
     */
    private $latitude;

    /**
     * @ORM\Column(type="text")
     */
    private $longitude;
      
    public function __construct()
    {
    }
     
    public function getId()
    {
        return $this->id;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
 
}
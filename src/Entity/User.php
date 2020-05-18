<?php

namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
 
/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Cet email est déjà enregistré en base.")
 * @UniqueEntity(fields="username", message="Cet identifiant est déjà enregistré en base")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=15, minMessage="Nom d'utilisateur trop court", maxMessage="Nom d'utilisateur de passe trop long")
     * @Assert\Regex(
     *     pattern="/\s/",
     *     match=false,
     *     message="Votre nom d'utilisateur contient des espaces."
     * )
     * @Assert\Regex(
     *     pattern="/[{}&@?%\/\\#]/",
     *     match=false,
     *     message="Les signes ({, }, &, @, %, \, /, ? et #) sont interdits."
     * )
     * @Assert\Regex(
     *     pattern="/[áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]/",
     *     match=false,
     *     message="Les accents sont interdits."
     * )
     */
    private $username;
 
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=8, max=30, minMessage="Mot de passe trop court", maxMessage="Mot de passe trop long")
     * @Assert\Regex("/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9]).*$/", message="Votre mot de passe ne répond pas aux critères demandés.")
     */
    private $plainPassword;
 
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=60)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $lastname;
 
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passwordRequestedAt;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $token;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $registration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Length(max=200, maxMessage="Votre biographie est trop longue.")
     */
    private $biography;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $ipRegistration;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $lastEvent;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $ipLastLogin;

    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "Code Captcha incorrect."
     * )
     */
    protected $captchaCode;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscribe", mappedBy="user")
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscribe", mappedBy="subscription")
     */
    private $subscriptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReqUserPrivate", mappedBy="user")
     */
    private $sendersPrv;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReqUserPrivate", mappedBy="userTarget")
     */
    private $targetsPrv;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user")
     */
    private $sendersMsg;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="userReceiveMsg")
     */
    private $targetsMsg;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="user")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author")
     */
    private $posts;

    public function __construct()
    {
        $this->isActive = true;
        $this->roles = ['ROLE_USER'];
        $this->registration = new \DateTime($this->registration, new \DateTimeZone('Europe/Paris'));
        $this->ipRegistration = $_SERVER["REMOTE_ADDR"];
        $this->biography = 'Aucune biographie pour le moment.';
        $this->score = 0;
        $this->private = 0;
        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->sendersPrv = new ArrayCollection();
        $this->targetsPrv = new ArrayCollection();
        $this->sendersMsg = new ArrayCollection();
        $this->targetsMsg = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    /**
     * @return Collection|Subscribers[]
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    /**
     * @return Collection|Subscriptions[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    /**
     * @return Collection|SendersPrv[]
     */
    public function getSendersPrv(): Collection
    {
        return $this->sendersPrv;
    }

    /**
     * @return Collection|TargetsPrv[]
     */
    public function getTargetsPrv(): Collection
    {
        return $this->targetsPrv;
    }

    /**
     * @return Collection|SendersMsg[]
     */
    public function getSendersMsg(): Collection
    {
        return $this->sendersMsg;
    }

    /**
     * @return Collection|TargetsMsg[]
     */
    public function getTargetsMsg(): Collection
    {
        return $this->targetsMsg;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @return Collection|Event[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
     
    /*
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }
 
    public function getUsername()
    {
        return $this->username;
    }
 
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
 
 
    public function getPassword()
    {
        return $this->password;
    }
 
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
 
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
 
    /*
     * Get email
     */
    public function getEmail()
    {
        return $this->email;
    }
 
    /*
     * Set email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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

    public function getFirstname()
    {
        return $this->firstname;
    }
 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }
 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }
 
    /*
     * Get isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
 
    /*
     * Set isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    // modifier la méthode getRoles
    public function getRoles()
    {
        return $this->roles; 
    }
 
    public function setRoles(array $roles)
    {
        if (!in_array('ROLE_USER', $roles))
        {
            $roles[] = 'ROLE_USER';
        }
        foreach ($roles as $role)
        {
            if(substr($role, 0, 5) !== 'ROLE_') {
                throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
            }
        }
        $this->roles = $roles;
        return $this;
    }

    /*
     * Get passwordRequestedAt
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /*
     * Set passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }

    /*
     * Get token
     */
    public function getToken()
    {
        return $this->token;
    }

    /*
     * Set token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }
 
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
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

    public function getRegistration()
    {
        return $this->registration;
    }

    public function getLastEvent()
    {
        return $this->lastEvent;
    }

    public function setLastEvent($lastEvent)
    {
        $this->lastEvent = $lastEvent;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;
        return $this;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    public function getPrivate()
    {
        return $this->private;
    }

    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    public function getIpRegistration()
    {
        return $this->ipRegistration;
    }

    public function getIpLastLogin()
    {
        return $this->ipLastLogin;
    }
 
    public function setIpLastLogin($ipLastLogin)
    {
        $this->ipLastLogin = $ipLastLogin;
        return $this;
    }

    public function getCaptchaCode()
    {
      return $this->captchaCode;
    }
  
    public function setCaptchaCode($captchaCode)
    {
      $this->captchaCode = $captchaCode;
      return $this;
    }

    public function getPostalCode()
    {
      return $this->postalCode;
    }
  
    public function setPostalCode($postalCode)
    {
      $this->postalCode = $postalCode;
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
 
    public function getSalt()
    {
        // pas besoin de salt puisque nous allons utiliser bcrypt
        // attention si vous utilisez une méthode d'encodage différente !
        // il faudra décommenter les lignes concernant le salt, créer la propriété correspondante, et renvoyer sa valeur dans cette méthode
        return null;
    }
 
    public function eraseCredentials()
    {
    }
 
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt,
        ));
    }
 
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt
        ) = unserialize($serialized);
    }
 
}
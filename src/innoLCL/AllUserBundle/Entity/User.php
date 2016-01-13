<?php
// src/innoLCL/AllUserBundle/Entity/User.php

namespace innoLCL\AllUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Entity\User as BaseUser;

/**
* @ORM\Entity
* @ORM\Table(name="u5s3e4r54ert")
* @ORM\Entity(repositoryClass="innoLCL\AllUserBundle\Entity\UserRepository")
*/
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $this->nbideaposted = 0;   
        $this->dateVote = new \DateTime();
    }
    
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
    protected $id;
       
    /**
     * @var boolean
     *
     * @ORM\Column(name="cgvaccepted", type="boolean")
     * 
     * @Assert\IsTrue(message = "X merci de cocher la case")
     */
     protected $cgvaccepted;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
     protected $lastname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="userfirstname", type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
     protected $firstname;
     
     /**
     * @Assert\Regex(
     *     pattern="/@(freetouch\.fr|yopmail\.com|lcl\.fr|lcl\.com|themisbanque\.com|interfimo\.fr|angle-neuf\.fr)$/si",
     *     match=true,
     *     message="Votre email n'appartient pas à un domaine autorisé."
     * )
     */
     protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", columnDefinition="enum('Réseaux (Part, Pros, BP, BEGF)', 'Fonctions supports', 'Traitement (DSBA) et Informatiques (ITP)')")
     * 
     * @Assert\Choice(choices = {"Réseaux (Part, Pros, BP, BEGF)","Fonctions supports", "Traitement (DSBA) et Informatiques (ITP)"})
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", columnDefinition="enum('SIEGE','IDF OUEST','IDF NORD','IDF SUD','MEDITERANNEE','MIDI','NORD OUEST','OUEST','SUD OUEST','ANTILLES GUYANE','EST','RHONE ALPES AUVERGNE')")
     * 
     * @Assert\Choice(choices = {"SIEGE","IDF OUEST","IDF NORD","IDF SUD","MEDITERANNEE","MIDI","NORD OUEST","OUEST","SUD OUEST","ANTILLES GUYANE","EST","RHONE ALPES AUVERGNE"})
     */
    private $region;


    /**
     * @var boolean
     *
     * @ORM\Column(name="videoseenon", type="boolean", nullable=true)
     * 
     */
    private $videoseenon;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbideaposted", type="integer")
     * 
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $nbideaposted;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="vote", type="integer", nullable=true)
     * 
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $vote;
    
    
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registeredAt;


    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set direction
     *
     * @param string $direction
     *
     * @return User
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set videoseenon
     *
     * @param \DateTime $videoseenon
     *
     * @return User
     */
    public function setVideoseenon($videoseenon)
    {
        $this->videoseenon = $videoseenon;

        return $this;
    }

    /**
     * Get videoseenon
     *
     * @return \DateTime
     */
    public function getVideoseenon()
    {
        return $this->videoseenon;
    }

    /**
     * Set postedon
     *
     * @param \DateTime $postedon
     *
     * @return User
     */
    public function setPostedon($postedon)
    {
        $this->postedon = $postedon;

        return $this;
    }

    /**
     * Get postedon
     *
     * @return \DateTime
     */
    public function getPostedon()
    {
        return $this->postedon;
    }

    /**
     * Set nbideaposted
     *
     * @param integer $nbideaposted
     *
     * @return User
     */
    public function setNbideaposted($nbideaposted)
    {
        $this->nbideaposted = $nbideaposted;

        return $this;
    }

    /**
     * Get nbideaposted
     *
     * @return integer
     */
    public function getNbideaposted()
    {
        return $this->nbideaposted;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     *
     * @return User
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set cgvaccepted
     *
     * @param boolean $cgvaccepted
     *
     * @return User
     */
    public function setCgvaccepted($cgvaccepted)
    {
        $this->cgvaccepted = $cgvaccepted;

        return $this;
    }

    /**
     * Get cgvaccepted
     *
     * @return boolean
     */
    public function getCgvaccepted()
    {
        return $this->cgvaccepted;
    }
    
    /**
     * Override SET EMAIL
     * Set username = email
     */
    public function setEmail($email)
{
    $email = is_null($email) ? '' : $email;
    parent::setEmail($email);
    $this->setUsername($email);

    return $this;
}


    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return User
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }
        
    /**
     * Set registeredAt
     *
     * @param \DateTime $registeredAt
     *
     * @return User
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registeredAt
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }
}

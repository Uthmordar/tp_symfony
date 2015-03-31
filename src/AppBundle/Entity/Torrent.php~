<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Torrent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TorrentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Torrent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *    min = 2,
     *    max = 200,
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="magnet", type="string", length=255)
     */
    private $magnet;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *    min = 40,
     *    max = 40
     * )
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="seeders", type="string", length=255)
     */
    private $seeders;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="leetchers", type="string", length=255)
     */
    private $leetchers;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="quality", type="string", length=255)
     */
    private $quality;

    /**
     * @var boolean
     *
     * @ORM\Column(name="block", type="boolean")
     */
    private $block=false;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;
    
        
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Movie", inversedBy="torrents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Torrent
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set magnet
     *
     * @param string $magnet
     * @return Torrent
     */
    public function setMagnet($magnet)
    {
        $this->magnet = $magnet;

        return $this;
    }

    /**
     * Get magnet
     *
     * @return string 
     */
    public function getMagnet()
    {
        return $this->magnet;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Torrent
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set seeders
     *
     * @param string $seeders
     * @return Torrent
     */
    public function setSeeders($seeders)
    {
        $this->seeders = $seeders;

        return $this;
    }

    /**
     * Get seeders
     *
     * @return string 
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * Set leetchers
     *
     * @param string $leetchers
     * @return Torrent
     */
    public function setLeetchers($leetchers)
    {
        $this->leetchers = $leetchers;

        return $this;
    }

    /**
     * Get leetchers
     *
     * @return string 
     */
    public function getLeetchers()
    {
        return $this->leetchers;
    }

    /**
     * Set quality
     *
     * @param string $quality
     * @return Torrent
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return string 
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set block
     *
     * @param boolean $block
     * @return Torrent
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return boolean 
     */
    public function getBlock()
    {
        return $this->block;
    }
    
    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Torrent
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set movie
     *
     * @param \AppBundle\Entity\Movie $movie
     * @return Torrent
     */
    public function setMovie(\AppBundle\Entity\Movie $movie){
        $this->movie = $movie;
        $movie->addTorrent($this);
        
        return $this;
    }

    /**
     * Get movie
     *
     * @return \AppBundle\Entity\Movie 
     */
    public function getMovie()
    {
        return $this->movie;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function prePersistCb(){
        $date=new \DateTime();
        $this->setDateCreated($date);
    }
}

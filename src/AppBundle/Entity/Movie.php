<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movie
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MovieRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Movie{
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Length(
     *    min = 2,
     *    max = 200,
     * )
     * @ORM\Column(name="imdb_id", type="integer")
     */
    private $imdbId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="director", type="string", length=255)
     */
    private $director;

    /**
     * @var string
     * @Assert\Url()
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 6.0
     * )
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="rating_count", type="integer")
     */
    private $ratingCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen=false;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Torrent", mappedBy="movie", cascade={"persist", "remove"})
     */
    private $torrents;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", mappedBy="movies", cascade={"persist"})
     */
    private $categories;

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
     * Set title
     *
     * @param string $title
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set imdbId
     *
     * @param integer $imdbId
     * @return Movie
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return integer 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Movie
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Movie
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Movie
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set ratingCount
     *
     * @param integer $ratingCount
     * @return Movie
     */
    public function setRatingCount($ratingCount)
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    /**
     * Get ratingCount
     *
     * @return integer 
     */
    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    /**
     * Set seen
     *
     * @param boolean $seen
     * @return Movie
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean 
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * Set block
     *
     * @param boolean $block
     * @return Movie
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
     * Constructor
     */
    public function __construct()
    {
        $this->torrents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     * @return Movie
     */
    public function addTorrent(\AppBundle\Entity\Torrent $torrents){
        $this->torrents[] = $torrents;

        return $this;
    }

    /**
     * Remove torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     */
    public function removeTorrent(\AppBundle\Entity\Torrent $torrents){
        $this->torrents->removeElement($torrents);
    }

    /**
     * Get torrents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTorrents(){
        return $this->torrents;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function prePersistCb(){
        $date=new \DateTime();
        $this->setDateCreated($date);
    }

    /**
     * Add categories
     *
     * @param \AppBundle\Entity\Category $categories
     * @return Movie
     */
    public function addCategory(\AppBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
        $categories->addMovie($this);
        
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \AppBundle\Entity\Category $categories
     */
    public function removeCategory(\AppBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}

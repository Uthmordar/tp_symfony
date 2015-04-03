<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 */
class Category{
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Movie", inversedBy="categories", cascade={"persist"})
     */
    private $movies;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name){
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName(){
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct(){
        $this->movies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add movies
     *
     * @param \AppBundle\Entity\Movie $movies
     * @return Category
     */
    public function addMovie(\AppBundle\Entity\Movie $movies){
        $this->movies[] = $movies;

        return $this;
    }

    /**
     * Remove movies
     *
     * @param \AppBundle\Entity\Movie $movies
     */
    public function removeMovie(\AppBundle\Entity\Movie $movies){
        $this->movies->removeElement($movies);
    }

    /**
     * Get movies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovies(){
        return $this->movies;
    }
}
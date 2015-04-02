<?php
namespace AppBundle\Services;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Torrent;
use AppBundle\Entity\Category;

class RegisterFromParserServices{
        
    protected $doctrine;
    protected $validator;
    
    protected $manager;
    protected $movieRepo;
    protected $torrentRepo;
    protected $categoryRepo;
    
    public function __construct($doctrine, $validator){
        $this->validator=$validator;
        
        $this->doctrine=$doctrine;
        
        $this->manager=$this->doctrine->getManager();
        
        $this->movieRepo=$this->doctrine->getRepository('AppBundle:Movie');
        $this->torrentRepo=$this->doctrine->getRepository('AppBundle:Torrent');
        $this->categoryRepo=$this->doctrine->getRepository('AppBundle:Category');
    }
    
    /**
     * get parser datas and register if fields are presents
     * @param type $data
     * @return boolean
     */
    public function registerDatas($data){
        foreach($data as $d){
            if(!empty($d['imdbId']) && !empty($d['director']) && !empty($d['rating']) && !empty($d['votes']) 
                && !empty($d['title']) && !empty($d['year']) && !empty($d['hash']) && !empty($d['leechers'])
                    && !empty($d['magnet']) && !empty($d['ancre']) && !empty($d['quality']) && !empty($d['seeders']) && $d['rating']>6 && !in_array($d['qualityType'], ['cam', 'ts'])){
                $this->registerData($d);
            }
        }
        return true;
    }
    
    /**
     * register dataset as movie // torrent
     * @param type $d
     */
    public function registerData($d){
        $categories=$this->registerCategories($d['genre']);
        $movie=$this->registerMovie($d, $categories);
        if($movie){
            $torrent=$this->registerTorrent($movie, $d);
        }
        if($movie && $torrent){
            $this->manager->flush();
        }
    }
    
    /**
     * register all categories in data table
     * @param type $cats
     * @return array
     */
    public function registerCategories($cats){
        $categories=[];
        foreach($cats as $cat){
            $this->registerCategory($categories, $cat);
        }
        
        return $categories;
    }
    
    /**
     * register category for a movie if not exist else return existing category
     * @param type $categories
     * @param type $cat
     */
    public function registerCategory(&$categories, $cat){
        $result=$this->categoryRepo->findCategoryByName($cat);
        if(!empty($result)){
            $categories[]=$result[0];
        }else{
            $categorie=new Category();
            $categorie->setName($cat);
            if(count($this->validator->validate($categorie))<1){
                $this->manager->persist($categorie);
                $categories[]=$categorie;
            }
        }
    }
    
    /**
     * register/update movie by imdbid
     * @param type $d
     * @param array $categories
     * @return type
     */
    public function registerMovie($d, $categories){
        $result=$this->movieRepo->findMovieByImdb($d['imdbId']);
                
        if(!empty($result)){
            $movie=$this->updateMovie($d, $result[0]);
        }else{
            $movie=$this->generateNewMovie($d, $categories);
        }
        
        return $this->validateEntity($movie);
    }
    
    /**
     * register/update torrent by name
     * @param \AppBundle\Entity\Movie $movie
     * @param type $d
     * @return type
     */
    public function registerTorrent(\AppBundle\Entity\Movie $movie, $d){
        $resultT=$this->torrentRepo->findTorrentByName($d['ancre']);
                
        if(!empty($resultT)){
            $torrent=$this->updateTorrent($d, $resultT[0]);
        }else{
            $torrent=$this->generateNewTorrent($d, $movie);
        }
        
        return $this->validateEntity($torrent);
    }
    
    /**
     * validate an entity
     * @param type $entity
     * @return boolean
     */
    public function validateEntity($entity){
        if(count($this->validator->validate($entity))<1){
            $this->manager->persist($entity);
            return $entity;
        }else{
            return false;
        }
    }
    
    /**
     * @param type $data
     * @param type $categories
     * @return Movie
     */
    public function generateNewMovie($data, $categories){
        $movie=new Movie();

        $movie->setDirector($data['director'])
            ->setImage($data['image'])
            ->setImdbId($data['imdbId'])
            ->setRating($data['rating'])
            ->setRatingCount($data['votes'])
            ->setTitle(trim(str_replace('"', "'", $data['title'])))
            ->setYear($data['year'])
            ->addCategory($categories);
        
        return $movie;
    }
    
    /**
     * @param type $data
     * @param Movie $movie
     * @return Movie
     */
    public function updateMovie($data, \AppBundle\Entity\Movie $movie){
        $movie->setRating($data['rating'])
            ->setRatingCount($data['votes']);
        
        return $movie;
    }
    
    /**
     * @param type $data
     * @param Movie $movie
     * @return Torrent
     */
    public function generateNewTorrent($data, \AppBundle\Entity\Movie $movie){
        $torrent=new Torrent();

        $torrent->setHash($data['hash'])
            ->setLeetchers($data['leechers'])
            ->setMagnet($data['magnet'])
            ->setName($data['ancre'])
            ->setQuality($data['quality'])
            ->setSeeders($data['seeders'])
            ->setQualityType($data['qualityType'])
            ->setMovie($movie);
        
        return $torrent;
    }
    
    /**
     * @param type $data
     * @param Torrent $torrent
     * @return Torrent
     */
    public function updateTorrent($data, \AppBundle\Entity\Torrent $torrent){
        $torrent->setSeeders($data['seeders'])
            ->setLeetchers($data['leechers']);
        
        return $torrent;
    }
}
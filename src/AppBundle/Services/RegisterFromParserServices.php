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
                    && !empty($d['magnet']) && !empty($d['ancre']) && !empty($d['quality']) && !empty($d['seeders']) && !in_array($d['qualityType'], ['cam', 'ts'])){
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
            $this->registerTorrent($movie, $d);
        }
        
        $this->manager->flush();
    }
    
    public function registerCategories($cats){
        $categories=[];
        foreach($cats as $cat){
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
        
        return $categories;
    }
    
    /**
     * register/update movie by imdbid
     * @param type $d
     * @return type
     */
    public function registerMovie($d, $categories){
        $result=$this->movieRepo->findMovieByImdb($d['imdbId']);
                
        if(!empty($result)){
            $movie=$result[0];

            $movie->setRating($d['rating']);
            $movie->setRatingCount($d['votes']);
        }else{
            $movie=new Movie();

            $movie->setDirector($d['director']);
            if(!empty($d['image'])){
                $movie->setImage($d['image']);
            }
            $movie->setImdbId($d['imdbId']);
            $movie->setRating($d['rating']);
            $movie->setRatingCount($d['votes']);
            $movie->setTitle(trim(str_replace('"', "'", $d['title'])));
            $movie->setYear($d['year']);
            foreach($categories as $cat){
                $movie->addCategory($cat);
            }
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
            $torrent=$resultT[0];
            $torrent->setSeeders($d['seeders']);
            $torrent->setLeetchers($d['leechers']);
        }else{
            $torrent=new Torrent();

            $torrent->setHash($d['hash']);
            $torrent->setLeetchers($d['leechers']);
            $torrent->setMagnet($d['magnet']);
            $torrent->setName($d['ancre']);
            $torrent->setQuality($d['quality']);
            $torrent->setSeeders($d['seeders']);
            $torrent->setQualityType($d['qualityType']);
            $torrent->setMovie($movie);
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
}
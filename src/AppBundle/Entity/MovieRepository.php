<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MovieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovieRepository extends EntityRepository{
    
    public function findMovieByImdb($imdb){
        $qb=$this->createQueryBuilder("m");
        
        $query=$qb->where('m.imdbId=:imdb')
            ->setParameter('imdb', $imdb)
            ->getQuery();
        
        return $query->getResult();
    }
}

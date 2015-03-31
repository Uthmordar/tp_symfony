<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository{
    public function findCategoryByName($name){
        $qb=$this->createQueryBuilder("t");
        
        $query=$qb->where('t.name LIKE :name')
            ->setParameter('name', "%$name%")
            ->getQuery();
        
        return $query->getResult();
    }
}

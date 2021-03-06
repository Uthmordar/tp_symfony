<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * TorrentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TorrentRepository extends EntityRepository{
    protected $nbTorrentHomeByPage=5;
    
    /** 
     * @param type $name
     * @return type
     */
    public function findTorrentByName($name){
        $qb=$this->createQueryBuilder("t");

        $query=$qb->where('t.name LIKE :name')
            ->setParameter('name', "%$name%")
            ->getQuery();

        return $query->getResult();
    }
    
    /**
     * @param type $p
     * @return Paginator
     */
    public function findNoBlockNoSeenTorrent($p){
        $query=$this->getEntityManager()
            ->createQuery('
                SELECT t, m FROM AppBundle:Torrent t
                JOIN t.movie m
                WHERE m.block=0 AND m.seen=0 AND t.block=0
                ORDER BY t.dateCreated DESC'
            )->setMaxResults($this->nbTorrentHomeByPage)
            ->setFirstResult($this->nbTorrentHomeByPage * ($p-1));

        $paginator=new Paginator($query);
        return $paginator;
    }

    public function getNbTorrentHomeByPage(){
        return $this->nbTorrentHomeByPage;
    }
}
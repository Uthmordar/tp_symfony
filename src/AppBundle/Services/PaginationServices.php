<?php
namespace AppBundle\Services;

class PaginationServices{
    protected $nb_page;
    
    public function __construct($nb_page){
        $this->nb_page=$nb_page;
    }
    
    public function getPaginationData($p, $tot, $nbPage){
        $page=ceil($tot/$nbPage);
        
        return ['page'=>$page,
            'p'=>$p,
            'tot'=>$tot,
            'nbPage'=>$nbPage,
            'start'=>($p-$this->nb_page>1)? $p-$this->nb_page : 1,
            'stop'=>($p+$this->nb_page<$page)? $p+$this->nb_page : $page,
            'hasPrevPage'=> ($p==1)? 1 : 0,
            'hasNextPage'=> ($p==$page)? 1 : 0,
            'firstResult'=> ($p-1)*$nbPage + 1,
            'lastResult'=> ($p==$page)? ($p-1)*$nbPage + $page % $nbPage + 1 : ($p-1)*$nbPage + $nbPage];
    }
}
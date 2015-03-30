<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Torrent;
use AppBundle\Entity\Movie;

class TorrentController extends Controller{
    /**
     * @Route("/{p}", defaults={"p"=1}, name="indexTorrent")
     */
    public function indexAction(){
        $pagination=$this->get('pagination_service');
        
        $torrentRepo=$this->getDoctrine()->getRepository("AppBundle:Torrent");

        $torrents=$torrentRepo->findNoBlockNoSeenTorrent();

        var_dump($torrents);
        
        //$data=$pagination->getPaginationData($p, count($actus), $actuRepo->getNbActusPerPage());
        
        
        /*$param=[
            'actus'=>$actus,
            'filter'=>$title,
        ];
        
        $params=array_merge($param, $data);*/
        
        return $this->render('actu/index_actu.html.twig');
    }
}

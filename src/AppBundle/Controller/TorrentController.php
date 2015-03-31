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
    public function indexAction($p){
        $pagination=$this->get('pagination_service');
        
        $torrentRepo=$this->getDoctrine()->getRepository("AppBundle:Torrent");

        $torrents=$torrentRepo->findNoBlockNoSeenTorrent($p);
        
        $data=$pagination->getPaginationData($p, count($torrents), $torrentRepo->getNbTorrentHomeByPage());
        
        
        $param=[
            'torrents'=>$torrents,
        ];
        
        $params=array_merge($param, $data);
        
        return $this->render('torrent/index.torrent.html.twig', $params);
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    
    /**
     * @Route("/torrent/blacklist/{id}", name="blacklistTorrent")
     * @param type $id
     */
    public function blacklistAction($id){
        $torrentRepo=$this->getDoctrine()->getRepository("AppBundle:Torrent");
        $torrent=$torrentRepo->find($id);
        
        if(!$torrent){
            $this->addFlash("error", "no torrent with this id");
            return $this->redirectToRoute("indexTorrent");
        }
        
        $em=$this->getDoctrine()->getManager();
        $torrent->setBlock(1);
        $em->flush();
        
        $this->addFlash("success", "torrent blacklisted");

        return $this->redirectToRoute("indexTorrent");
    }
}
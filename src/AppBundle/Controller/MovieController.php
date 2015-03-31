<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Torrent;
use AppBundle\Entity\Movie;

class MovieController extends Controller{
    
    /**
     * @Route("/movie/{id}", name="showMovie")
     * @param type $id
     */
    public function showAction($id){
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie=$movieRepo->find($id);
        
        if(!$movie){
            $this->addFlash("error", "no movie with this id");
            return $this->redirectToRoute("indexTorrent");
        }
        
        $params=[
            'movie'=>$movie
        ];

        return $this->render('movie/show.movie.html.twig', $params);
    }
}
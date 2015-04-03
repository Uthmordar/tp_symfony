<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    
    /**
     * @Route("/movie/blacklist/{id}", name="blacklistMovie")
     * @param type $id
     */
    public function blacklistAction($id){
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie=$movieRepo->find($id);

        if(!$movie){
            $this->addFlash("error", "no movie with this id");
            return $this->redirectToRoute("indexTorrent");
        }

        $em=$this->getDoctrine()->getManager();
        $movie->setBlock(1);
        $em->flush();

        $this->addFlash("success", "movie blacklisted");

        return $this->redirectToRoute("indexTorrent");
    }
    
    /**
     * @Route("/movie/seen/{id}", name="seenMovie")
     * @param type $id
     */
    public function seenAction($id){
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie=$movieRepo->find($id);

        if(!$movie){
            $this->addFlash("error", "no movie with this id");
            return $this->redirectToRoute("indexTorrent");
        }

        $em=$this->getDoctrine()->getManager();
        $status=($movie->getSeen())? 0 : 1;
        $movie->setSeen($status);
        $em->flush();

        $this->addFlash("success", $movie->getTitle() . " blacklisted");

        return $this->redirectToRoute("showMovie", ['id'=>$id]);
    }
}
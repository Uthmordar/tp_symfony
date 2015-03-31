<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller{
    /**
     * @Route("/category-{name}/{p}", defaults={"p"=1}, name="showCategory")
     */
    public function indexAction($name, $p){
        $pagination=$this->get('pagination_service');
        
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $catRepo=$this->getDoctrine()->getRepository("AppBundle:Category");

        $movies=$movieRepo->findMoviesByCategory($name, $p);

        $data=$pagination->getPaginationData($p, count($movies), $movieRepo->getNbMovieByPage());
              
        $param=[
            'category'=>$catRepo->findCategoryByName($name)[0],
            'movies'=>$movies
        ];
        
        $params=array_merge($param, $data);
        
        return $this->render('category/show.category.html.twig', $params);
    }
}
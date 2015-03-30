<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MovieController extends Controller{
    /**
     * @Route("/movie/", name="movieAll")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}

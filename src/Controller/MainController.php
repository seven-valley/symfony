<?php

namespace App\Controller;

use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="home" ,methods={"GET"})
     * 
     */
    public function home(PersonneRepository $repo): Response
    {
        $personnes = $repo->findAll();
        return $this->render('main/home.html.twig',compact('personnes'));
    }
    
   
}

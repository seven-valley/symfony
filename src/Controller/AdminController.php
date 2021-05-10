<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function home(): Response
    {
        return $this->render('admin/home.html.twig');
    }
    /**
     * @Route("/admin/personnes", name="admin_personnes")
     */
    public function personnes(
        EntityManagerInterface $em,
        PersonneRepository $repo,
        Request $request): Response
    {
        
        $personne = new Personne();//je créer un bo
        // on associe le bo au formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        // traiter le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            // Quand j'ajoute une personne
            // je considére que elle viendra
            
            $personne->setStatus(true);
            $em->persist($personne);
            $em->flush();
            return $this->redirectToRoute('admin_personnes'); 
        }
        $personnes = $repo->findByStatus();

        return $this->render('admin/personnes.html.twig',[
            'personnes' => $personnes,
            'personneForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/delete-personne/{id}", name="delete_personne")
     */
    public function delete_personne(EntityManagerInterface $em, Personne $personne): Response
    {
       $em->remove($personne);
       $em->flush();
       return $this->redirectToRoute('admin_personnes'); 
    }
    /**
     * @Route("/admin/etat/{id}", name="change_etat")
     */
    public function change_etat(EntityManagerInterface $em, Personne $personne): Response
    {
       if ($personne->getStatus())
       {
        $personne->setStatus(false);
       }
       else
       {
        $personne->setStatus(true);
       }
       $em->flush();
       return $this->redirectToRoute('admin_personnes'); 
    }
    /**
     * @Route("/admin/modifier-personne/{id}", 
            name="modifier_personne", 
            requirements={"id":"\d+"},
            methods={"GET","POST"})
     */
    public function modifier_personne(
        Request $request,
        EntityManagerInterface $em, 
        Personne $personne): Response
    {
       
        // on associe le bo au formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        // traiter le formulaire
        // je viens hydrater l'objet $personne
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $message = 'Personne modifié !'.$personne->getNom()." ".$personne->getPrenom();
            $this->addFlash('success' ,$message);
            $em->flush();
            return $this->redirectToRoute('admin_personnes'); 
        }

       return $this->render('admin/modifier.html.twig',[
            'personneForm' => $form->createView()
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use App\Form\PersonneType;
use App\Form\UserType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/bo", name="admin")
     */
    public function home(): Response
    {
        return $this->render('admin/home.html.twig');
    }
    /**
     * @Route("/bo/personnes", name="admin_personnes")
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
     * @Route("/bo/delete-personne/{id}", name="delete_personne")
     */
    public function delete_personne(EntityManagerInterface $em, Personne $personne): Response
    {
       $em->remove($personne);
       $em->flush();
       return $this->redirectToRoute('admin_personnes'); 
    }
    /**
     * @Route("/bo/etat/{id}", name="change_etat")
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
     * @Route("/bo/modifier-personne/{id}", 
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
    /**
     * @Route("/admin/list-user", name="admin_liste_user")
     */
    public function listeUser(UserRepository $repo): Response
    {
        
       return $this->render('admin/list-user.html.twig',[
            'users' => $repo->findAll()
        ]);
   }
    /**
     * @Route("/admin/change-user/{id}", name="admin_change_user")
     */
    public function changeUser(User $user, EntityManagerInterface $em): Response
    {
       // AdMIN ou USER ?
       $tab = $user->getRoles(); // ROLE_USER ROLE_ADMIN
       if ($tab[0] == 'ROLE_USER')
       {
        $user->setRoles(["ROLE_ADMIN"]);
       }
       else
       {
        $user->setRoles(["ROLE_USER"]);
       }
        $em->flush();

      return $this->redirectToRoute('admin_liste_user'); 
   }
    /**
     * @Route("/admin/delete/user/{id}", name="delete_user")
     */
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

      return $this->redirectToRoute('admin_liste_user'); 
   }

    /**
     * @Route("/admin/edit/user/{id}", name="edit_user")
     */
    public function editUser(User $user,Request $req, EntityManagerInterface $em): Response
    {
        $form =$this->createForm(UserType::class,$user);
        $form->handleRequest($req);
        if ($form->isSubmitted())
        {
            $message = 'Utilisateur modifié !'.$user->getNom()." ".$user->getPrenom();
            $this->addFlash('success' ,$message);
            $em->flush();
            return $this->redirectToRoute('admin_liste_user'); 
        }

        return $this->render('admin/user.html.twig',[
            'userForm' => $form->createView()
        ]);  
      
   }
}

<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    /**
     * @Route("/manageCity", name="manage_city")
     */
    public function manage()
    {
        // récupérer les villes depuis la base de données
        $villesRepo = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $villesRepo->findAll();

        return $this->render('ville/manage.html.twig', [
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/manageCity/update/{id}", name="manageID_city")
     */
    public function manageCityById(Request $request,EntityManagerInterface $em){
        // récupérer les villes depuis la base de données
        $villesRepo = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $villesRepo->findAll();

        $ville= $this->getDoctrine()->getRepository(Ville::class)->find($request->attributes->get('id'));

        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ville);
            $em->flush();
            return $this->redirectToRoute("manage_city");

        }
        return $this->render('ville/manage.html.twig', [
            'villes' => $villes,
            'villeForm'=>$form->createView(),
            'id'=>$request->attributes->get('id'),
        ]);
    }


    /**
     * Créer une ville
     * @Route("/manageCity/add", name="add_city")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $villesRepo = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $villesRepo->findAll();

        // traiter un formulaire
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            // sauvegarder les données dans la base
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', "La ville a été ajouté");
            return $this->redirectToRoute("manage_city");

        }
        return $this->render("ville/manage.html.twig", [
            "cityFormulaire" => $villeForm->createView(),
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/manageCity/delete/{id}", name="delete_city")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {
        $ville = $em->getRepository(Ville::class)->find($id);
        if ($ville == null) {
            throw $this->createNotFoundException('Ville inconnu ou déjà supprimé');
        }
        if ($this->isCsrfTokenValid('delete'.$ville->getId(),
            $request->request->get('_token'))) {
            $em->remove($ville);
            $em->flush();
            $this->addFlash('success', "La ville a été supprimé");
        }
        return $this->redirectToRoute("manage_city");
    }
}

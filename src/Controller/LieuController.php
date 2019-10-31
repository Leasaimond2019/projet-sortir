<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{


    /**
     * @Route("/managePlace", name="manage_place")
     */
    public function manage()
    {
        // récupérer les sites depuis la base de données
        $placesRepo = $this->getDoctrine()->getRepository(Lieu::class);
        $places = $placesRepo->findAll();

        return $this->render('lieu/manage.html.twig', [
            'places' => $places,
        ]);
    }

    /**
     * @Route("/lieu/create", name="lieuCreate")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu a été ajouté');
            return $this->render("sortie/create.html.twig", [
                'lieuForm'=>$lieuForm->createView()
            ]);
        }
    }

    /**
     * @Route("/managePlace/update/{id}", name="manageID_place")
     */
    public function managePlaceById(Request $request,EntityManagerInterface $em,$id){
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        // récupérer les places depuis la base de données
        $placesRepo = $this->getDoctrine()->getRepository(Lieu::class);
        $places = $placesRepo->findAll();

        $place= $this->getDoctrine()->getRepository(Lieu::class)->find($id);

        $form = $this->createForm(LieuType::class, $place);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($place);
            $em->flush();
            return $this->redirectToRoute("manage_place");

        }
        return $this->render('lieu/manage.html.twig', [
            'places' => $places,
            'placeForm'=>$form->createView(),
            'id'=>$id,
        ]);
    }


    /**
     * Créer un lieu
     * @Route("/managePlace/add", name="add_place")
     */
    public function createPlace(EntityManagerInterface $em, Request $request) {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $placesRepo = $this->getDoctrine()->getRepository(Lieu::class);
        $places = $placesRepo->findAll();

        // traiter un formulaire
        $place = new Lieu();
        $placeForm = $this->createForm(LieuType::class, $place);
        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            // sauvegarder les données dans la base
            $em->persist($place);
            $em->flush();
            $this->addFlash('success', "Le lieu a été ajouté");
            return $this->redirectToRoute("manage_place");

        }
        return $this->render("lieu/manage.html.twig", [
            "placeFormulaire" => $placeForm->createView(),
            'places' => $places,
        ]);
    }

    /**
     * @Route("/managePlace/delete/{id}", name="delete_place")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $place = $em->getRepository(Lieu::class)->find($id);
        if ($place == null) {
            throw $this->createNotFoundException('Lieu inconnu ou déjà supprimé');
        }
        if ($this->isCsrfTokenValid('delete'.$place->getId(),
            $request->request->get('_token'))) {
            $em->remove($place);
            $em->flush();
            $this->addFlash('success', "Le lieu a été supprimé");
        }
        return $this->redirectToRoute("manage_place");
    }
}

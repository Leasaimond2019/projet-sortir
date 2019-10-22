<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie_list")
     */
    public function list()
    {
        // récupérer les sorties depuis la base de données
        $sortiesRepo = $this->getDoctrine()->getRepository(Article::class);
        $sorties = $sortiesRepo->findAll();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/sortie/create",name="sortie_create")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été créée");
            return $this->redirectToRoute("home");
        }
        return $this->render("sortie/create.html.twig", [
            "sortieForm" => $sortieForm->createView()
        ]);
    }
}

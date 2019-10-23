<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SiteType;
use App\Form\SortieType;
use App\Form\UserType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use function Sodium\add;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie_list")
     */
    public function list()
    {
        // récupérer les sorties depuis la base de données
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortiesRepo->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/sortie/create",name="sortie_create")
     * @throws \Exception
     */
    public function create(EntityManagerInterface $em, Request $request) {

        // Lieu
        $lieu = $this->createLieuPopup($em,$request);

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            // on met l'état de la sortie à "créée"
            $numEtat = $em->getRepository(Etat::class)->findOneBy(["libelle"=>"Créée"]);
            $sortie->setNoEtat($numEtat);

            // ajout de l'user en cours
            $sortie->setNoOrganisateur($this->getUser());
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été créée");
            return $this->redirectToRoute("home");
        }
        return $this->render("sortie/create.html.twig", [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieu->createView()
        ]);
    }

    private function createLieuPopup(EntityManagerInterface $em, Request $request) {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu a été ajouté');
        }
        return $lieuForm;
    }

}

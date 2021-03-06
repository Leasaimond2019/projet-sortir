<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\SearchSortie;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SearchSortieType;
use App\Form\SiteType;
use App\Form\SortieType;
use App\Form\UserType;
use App\Repository\EtatRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class SortieController extends AbstractController
{
    /**
     * @Route("/list", name="sortie_list")
     */
    public function list(EntityManagerInterface $em, Request $request)
    {
        // Gérer la recherche
        $search = new SearchSortie();
        $searchForm = $this->createForm(SearchSortieType::class, $search);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $options = array();

            if (!is_null($search->getSite())) {
                $options[] = "s.no_site = " . $search->getSite()->getId();
            }
            if (!is_null($search->getNom())) {
                $options[] = "s.nom LIKE '%" . $search->getNom() . "%'";
            }
            if (!is_null($search->getDateDebut()) && !is_null($search->getDateFin())) {
                $options[] = "s.date_debut BETWEEN '" . $search->getDateDebut()->format("Ymdhis")
                    . "' AND '" . $search->getDateFin()->format("YmdHis") . "'";
            }
            if ($search->getChkOrganisateur()) {
                $options[] = "s.no_organisateur = " . $this->getUser()->getId();
            }
            if ($search->getChkInscrit() && !$search->getChkNonInscrit()) {
                $options[] = "i.no_user = " . $this->getUser()->getId();
            }
            if ($search->getChkNonInscrit() && !$search->getChkInscrit()) {
                $options[] = "i.no_user != " . $this->getUser()->getId() . " OR i.no_user IS NULL";
            }
            if ($search->getChkPasse()) {
                $now = new \DateTime("now");
                $options[] = "s.date_fin <= '" . $now->format("YmdHis") . "'";
            }
        }

        // récupérer les sorties depuis la base de données
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        if (isset($options)) {
            $sorties = $sortiesRepo->findBySeveralFields($options);
        } else {
            $sorties = $sortiesRepo->findAllExceptArchivee($this->getUser()->getId());
        }

        // Récupérer les inscriptions
        $inscriptions = $this->getDoctrine()->getRepository(Inscription::class);
        $inscriptions = $inscriptions->findAll();


        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'inscriptions' => $inscriptions,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/create",name="sortie_create")
     * @throws \Exception
     */
    public function create(EntityManagerInterface $em, Request $request)
    {

        // Pour l'ajout d'un nouveau lieu
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success', 'Le lieu a été ajouté');
            return $this->redirectToRoute("sortie_create");
        }

        // Création d'une sortie
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie,["modification"=>false]);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            // on met l'état de la sortie à "créée"
            $numEtat = $em->getRepository(Etat::class)->findOneBy(["libelle" => "Créée"]);
            $sortie->setNoEtat($numEtat);
            $sortie->setNoSite($this->getUser()->getNoSite());

            // ajout de l'user en cours
            $sortie->setNoOrganisateur($this->getUser());
            $sortie->setDateFin(clone $sortie->getDateDebut()->add(new \DateInterval('PT' . $sortie->getDuree() . 'M')));
            $sortie->getUrlPhoto() == null ? $sortie->setUrlPhoto("http://www.stleos.uq.edu.au/wp-content/uploads/2016/08/image-placeholder-350x350.png") : "";
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été créée");
            return $this->redirectToRoute("sortie_list");
        }
        return $this->render("sortie/create.html.twig", [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'user' => $this->getUser(),
            'sites' => $em->getRepository(Site::class)->findAll()
        ]);
    }

    /**
     * @Route("/details/{id}", name="sortie_detail",
     *     requirements={"id"="\d+"}, methods={"POST","GET"})
     */
    public function detail($id, Request $request)
    {
        // récupérer la fiche article dans la base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $dateheureFinSortie = $sortie->getDateFin();
        $dateCourante = new \DateTime("now");
        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        // update de l'état de la sortie
        if ($dateCourante > $dateheureFinSortie && $sortie->getNoEtat()->getLibelle() != "Annulée") {
            $sortie->setNoEtat($etatRepo->findOneBy(["libelle" => "Passée"]));
        } elseif ($sortie->getDateDebut() < $dateCourante && $dateCourante < $dateheureFinSortie && $sortie->getNoEtat()->getLibelle() != "Annulée") {
            $sortie->setNoEtat($etatRepo->findOneBy(["libelle" => "Activité en cours"]));
        }
        // inscriptions de la sortie
        $inscriptions = $sortie->getNoInscription();

        if ($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnue");
        }

        $inscriptionOuvertes = true;
        if (count($inscriptions) >= $sortie->getNbInscriptionMax()
            || $sortie->getDateCloture() < new \DateTime('now')) {
            $inscriptionOuvertes = false;
        }
        $noInscription = $this->getDoctrine()->getRepository(Inscription::class);
        $noInscription = $noInscription->findByUserAndSortie($this->getUser(), $sortie);
        if (!empty($noInscription)) {
            $inscriptionId = $noInscription[0]->getId();
        } else {
            $inscriptionId = null;
        }

        return $this->render("sortie/detail.html.twig", [
            "sortie" => $sortie,
            "inscriptions" => $inscriptions,
            "ouvert" => $inscriptionOuvertes,
            "inscriptionId" => $inscriptionId
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, $id)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if ($sortie == null) {
            throw $this->createNotFoundException('Sortie inconnue ou déjà supprimée');
        }
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(),
            $request->request->get('_token'))) {
            $em->remove($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été supprimée");
        }
        return $this->redirectToRoute("sortie_list");
    }

    /**
     * @Route("/messorties", name="messorties_list")
     */
    public function mylist()
    {
        // récupérer les sorties depuis la base de données
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortiesRepo->findSortieByUser($this->getUser()->getId());
        // Récupérer les inscriptions
        $inscriptions = $this->getDoctrine()->getRepository(Inscription::class);
        $inscriptions = $inscriptions->findAll();

        if (count($sorties) > 0) {
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'inscriptions' => $inscriptions,
                'mesSorties' => true
            ]);
        } else {
            return $this->render('sortie/list.html.twig', [
                'mesSorties' => true
            ]);
        }
    }

    /**
     * @Route("/sortie/cancel{id}", name="cancel_sortie")
     */
    public function cancelSortie($id, Request $request)
    {
        // récupérer la fiche article dans la base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if ($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnue");
        }

        return $this->render("sortie/cancel.html.twig", [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/sortie/Save{id}", name="save_sortie")
     */
    public function saveSortie($id, Request $request, EntityManagerInterface $em)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if ($sortie == null) {
            throw $this->createNotFoundException('Sortie inconnue');
        }

        if ($request->request->get('motif')) {
            $numEtat = $em->getRepository(Etat::class)->findOneBy(["libelle" => "Annulée"]);
            $sortie->setMotif($request->request->get('motif'));
            $sortie->setNoEtat($numEtat);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifiée");
            return $this->redirectToRoute("sortie_detail",
                ['id' => $sortie->getId()]);
        }
        return $this->render("sortie/cancel.html.twig", [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/edit{id}", name="edit_sortie")
     */
    public function editSortie($id, Request $request, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if ($sortie == null) {
            throw $this->createNotFoundException('Sortie inconnu');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie,["modification"=>true]);
        $sortieForm->handleRequest($request);



        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if($request->request->get('publish')!= null){

                $sortie->setNoEtat($this->getDoctrine()->getRepository(Etat::class)->findOneBy(["libelle"=>"Ouverte"]));
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', "La sortie a été publiée");
                return $this->redirectToRoute("sortie_detail",
                    ['id' => $sortie->getId()]);
            }
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifiée");
            return $this->redirectToRoute("sortie_detail",
                ['id' => $sortie->getId()]);
        }
        return $this->render("sortie/edit.html.twig", [
            "sortieForm" => $sortieForm->createView(),
            "sortie"=>$sortie,
        ]);

    }
}
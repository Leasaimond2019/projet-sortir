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
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

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
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            // on met l'état de la sortie à "créée"
            $numEtat = $em->getRepository(Etat::class)->findOneBy(["libelle" => "Créée"]);
            $sortie->setNoEtat($numEtat);
            $sortie->setNoSite($this->getUser()->getNoSite());

            // ajout de l'user en cours
            $sortie->setNoOrganisateur($this->getUser());
            $em->persist($sortie);
            $sortie->getUrlPhoto() == null ? $sortie->setUrlPhoto("http://www.stleos.uq.edu.au/wp-content/uploads/2016/08/image-placeholder-350x350.png") : "";
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
        $inscriptions = $sortie->getNoInscription();

        if ($sortie == null) {
            throw $this->createNotFoundException("Article inconnu");
        }

        return $this->render("sortie/detail.html.twig", [
            "sortie" => $sortie,
            "inscriptions" => $inscriptions
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

        if (count($sorties) > 0) {
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'mesSorties' => true
            ]);
        } else {
            return $this->render('sortie/list.html.twig', [
                'mesSorties' => true
            ]);
        }
    }

    /**
     * @Route("/sortie/Cancel{id}", name="cancel_sortie")
     */
    public function cancelSortie($id, Request $request)
    {
        // récupérer la fiche article dans la base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if ($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnu");
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
            throw $this->createNotFoundException('Sortie inconnu');
        }

        if ($request->request->get('motif')) {
            $numEtat = $em->getRepository(Etat::class)->findOneBy(["libelle" => "Annulée"]);
            $sortie->setMotif($request->request->get('motif'));
            $sortie->setNoEtat($numEtat);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifié");
            return $this->redirectToRoute("sortie_detail",
                ['id' => $sortie->getId()]);
        }
        return $this->render("sortie/cancel.html.twig", [
            "sortie" => $sortie
        ]);
    }
}

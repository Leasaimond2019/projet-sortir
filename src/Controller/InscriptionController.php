<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index()
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }
    /**
     * @Route("/sinscrire/{id}",name="inscription_create")
     */
        public function sinscrire(Request $request, EntityManagerInterface $em, $id) {
            $inscription = new Inscription();
            $sortie = $em->getRepository(Sortie::class)->find($id);
            $inscriptionsDansSortie = $sortie->getNoInscription();
            $userExistePourSortie = $em->getRepository(Inscription::class)->findByUserAndSortie($this->getUser(),$sortie);
            if(count($inscriptionsDansSortie) == $sortie->getNbInscriptionMax()) {
                $this->addFlash("danger","La sortie n'a plus de places disponibles");
            } else if(!empty($userExistePourSortie)) {
                $this->addFlash("danger", "Vous participez déjà à cette sortie");
            } else if($sortie->getDateCloture() > new \DateTime('now')) {
                $this->addFlash("danger", "La date limite d'inscription à cette sortie est dépassée");
            } else {
                $sortie->addNoInscription($inscription);
                $this->getUser()->addNoInscription($inscription);
                $inscription->setDateInscription(new \DateTime('now'));
                $em->persist($inscription);
                $em->flush();
            }
        return $this->redirectToRoute('sortie_detail', [
            'id'=>$sortie->getId()]
        );
    }

    /**
     * @Route("/inscription/sedesister/{id}", name="delete_inscription")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {
        $inscription = $em->getRepository(Inscription::class)->find($id);
        if ($inscription == null) {
            throw $this->createNotFoundException('Inscription inconnue ou déjà annulée');
        }
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(),
            $request->request->get('_token'))) {
            $em->remove($inscription);
            $em->flush();
            $this->addFlash('success', "Vous avez été désinscrit de cette sortie");}
        return $this->redirectToRoute("sortie_list");
    }
}


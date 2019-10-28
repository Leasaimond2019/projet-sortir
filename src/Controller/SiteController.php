<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{

    /**
     * @Route("/manageSite", name="manage_site")
     */
    public function manage()
    {
        // récupérer les sites depuis la base de données
        $sitesRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $sitesRepo->findAll();

        return $this->render('site/manage.html.twig', [
            'sites' => $sites,
        ]);
    }

    /**
     * @Route("/manageSite/update/{id}", name="manageID_site")
     */
    public function manageSiteById(Request $request,EntityManagerInterface $em){
        // récupérer les sites depuis la base de données
        $sitesRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $sitesRepo->findAll();

        $site= $this->getDoctrine()->getRepository(Site::class)->find($request->attributes->get('id'));

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();
            return $this->redirectToRoute("manage_site");

        }
        return $this->render('site/manage.html.twig', [
            'sites' => $sites,
            'siteForm'=>$form->createView(),
            'id'=>$request->attributes->get('id'),
        ]);
    }


    /**
     * Créer un site
     * @Route("/manageSite/add", name="add_site")
     */
    public function create(EntityManagerInterface $em, Request $request) {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $sitesRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $sitesRepo->findAll();

        // traiter un formulaire
        $site = new Site();
        $siteForm = $this->createForm(SiteType::class, $site);
        $siteForm->handleRequest($request);

        if ($siteForm->isSubmitted() && $siteForm->isValid()) {
            // sauvegarder les données dans la base
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', "Le site a été ajouté");
            return $this->redirectToRoute("manage_site");

        }
        return $this->render("site/manage.html.twig", [
            "siteFormulaire" => $siteForm->createView(),
            'sites' => $sites,
        ]);
    }

    /**
     * @Route("/manageSite/delete/{id}", name="delete_site")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {
        $site = $em->getRepository(Site::class)->find($id);
        if ($site == null) {
            throw $this->createNotFoundException('Site inconnu ou déjà supprimé');
        }
        if ($this->isCsrfTokenValid('delete'.$site->getId(),
            $request->request->get('_token'))) {
            $siteRepo=$em->getRepository(Site::class);


            if(count($siteRepo->findUserAdminInSite($site))>0 ){
                $this->addFlash('danger', "Le site est lié à un administrateur et ne peut pas être supprimé.");
            }else{
            $em->remove($site);
            $em->flush();
            $this->addFlash('success', "Le site a été supprimé");}
        }
        return $this->redirectToRoute("manage_site");
    }
}

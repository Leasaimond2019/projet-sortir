<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index()
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

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
     * @Route("/manageSite/{id}", name="manageID_site")
     */
    public function manageSiteById(Request $request){
        // récupérer les sites depuis la base de données
        $sitesRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $sitesRepo->findAll();

        $site= $this->getDoctrine()->getRepository(Site::class)->find($request->attributes->get('id'));

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        return $this->render('site/manage.html.twig', [
            'sites' => $sites,
            'siteForm'=>$form->createView(),
            'id'=>$request->attributes->get('id'),
        ]);
    }
}

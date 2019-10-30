<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/monCompte/profil", name="account_profile")
     */
    public function profileEdit(Request $request, ObjectManager $manager, UserRepository $user, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $request->request->get('user')['oldPassword'];
            // Si l'ancien mot de passe est bon
            if ($encoder->isPasswordValid($user, $oldPassword)) {
                if ($request->request->get('user')['password']['first'] != null && $request->request->get('user')['password']['first'] != "") {
                    $hash = $encoder->encodePassword($user, $request->request->get('user')['password']['first']);
                    $user->setPassword($hash);
                }
                $user->getPhoto() == null ? $user->setPhoto("http://www.stleos.uq.edu.au/wp-content/uploads/2016/08/image-placeholder-350x350.png") : "";
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre profil à bien été changé !');

                return $this->redirectToRoute('sortie_list');
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));

            }
        }

        return $this->render('user/editProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function register(EntityManagerInterface $em,
                             Request $request,
                             UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setActif(true);
            $user->getPhoto() == null ? $user->setPhoto("http://www.stleos.uq.edu.au/wp-content/uploads/2016/08/image-placeholder-350x350.png") : "";
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("sortie_list");

        }

        return $this->render('user/register.html.twig', [
            'registerForm' => $registerForm->createView(),
        ]);
    }

    /**
     * @Route("/profil/{id}", name="user_detail",
     *     requirements={"id"="\d+"}, methods={"POST","GET"})
     */
    public function detail($id, Request $request)
    {
        // récupérer la fiche article dans la base de données
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($id);

        if ($user == null) {
            throw $this->createNotFoundException("User inconnu");
        }

        return $this->render("user/detail.html.twig", [
            "user" => $user
        ]);
    }
}


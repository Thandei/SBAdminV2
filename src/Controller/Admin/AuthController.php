<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/admin/auth', name: 'app_admin_auth_')]
class AuthController extends AbstractController
{
    #[Route(path: '/signin', name: "signin")]
    public function authSignin(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
              return $this->redirectToRoute('app_admin_dashboard');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/auth/signin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/cards', name: 'cards')]
    public function cards(): Response
    {
        return $this->render('admin/dashboard/cards/index.html.twig');
    }

    #[Route(path: '/signout', name: 'signout')]
    public function logout(): void
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

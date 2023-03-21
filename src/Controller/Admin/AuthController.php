<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/admin/auth', name: 'app_admin_auth_')]
class AuthController extends AbstractController
{
    #[Route(path: '/signin', name: "signin")]
    public function authSignin(AuthenticationUtils $authenticationUtils, $request): Response
    {
         if ($this->getUser()) {
              return $this->redirectToRoute('app_admin_dashboard');
         }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // If User is not logged, create Google Client link for Sign In to Google
        $myGoogleClient = AuthController::getGoogleAPIClient($this->getParameter("app.auth"));
        $googleClientAuthURL = $myGoogleClient->createAuthUrl();

        // If Google Sign In link has come, check token.
        $googleClientCode = $request->get("code");
        if ($googleClientCode) {
            $token = $myGoogleClient->fetchAccessTokenWithAuthCode($googleClientCode);
            if (isset($token["access_token"])) {
                $myGoogleClient->setAccessToken($token['access_token']);
                $googleOAuth = new Google_Service_Oauth2($myGoogleClient);
                $googleAccountInfo = $googleOAuth->userinfo->get();
                $loggedEmail = $googleAccountInfo->getEmail();
                $loggedUserEntity = $userRepository->findOneBy(["email" => $loggedEmail]);
                if ($loggedUserEntity instanceof User) {
                    $security->login($loggedUserEntity, LoginFormAuthenticator::class);
                    return $this->redirectToRoute(LoginFormAuthenticator::LOGIN_SUCCESS_ROUTE);
                } else {
                    $error = new  UserNotFoundException("No account found for this email address.", 401);
                }
            } else {
                $error = new  AuthenticationException("Could not login to your Google account.", 400);
            }
        }

        return $this->render('admin/auth/signin.html.twig', ['last_username' => $lastUsername, 'error' => $error, "googleAuthURL" => $googleClientAuthURL]);
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

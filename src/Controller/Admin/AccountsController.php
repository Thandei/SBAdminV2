<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
class AccountsController extends AbstractController
{


    #[Route('/accounts/overview', name: 'accounts_overview')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {

        return $this->render('admin/accounts/index.html.twig');


    }

    #[Route('/accounts/{id}', methods: ["GET"])]
    #[IsGranted("SHOW", subject: "account")]
    public function show(Account $account): Response
    {

      return $this->render("accounts/index.html.twig", ["account" => $account]);
    }

    #[Route('/accounts/{id}/delete',name:"delete_account" ,methods: ["GET"])]
    #[IsGranted("DELETE", subject: "account")]
    public function delete(Account $account): Response
    {

            return new Response("Deleting account". $account->getId());

    }


}

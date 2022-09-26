<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_user")
     */
    public function MyProfile(): Response
    {
        return $this->render('user/MyProfile.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

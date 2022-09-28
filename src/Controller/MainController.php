<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
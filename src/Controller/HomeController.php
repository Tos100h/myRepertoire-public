<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    /**
     * @Route("/demo", name="app_home_demo")
     */
    public function demo(): Response
    {
        return $this->render('home/demo.html.twig', [
        ]);
    }
}

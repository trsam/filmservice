<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $databaseUrl = getenv('DATABASE_URL');
        

        // ...

        return $this->render('my/index.html.twig', [
            'database_url' => $databaseUrl,
        ]);
    }
}
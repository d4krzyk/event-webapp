<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Kontroler obsługujący stronę główną aplikacji.
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    /**
     * Wyświetla stronę główną.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}

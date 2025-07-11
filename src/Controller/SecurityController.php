<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Kontroler obsługujący logowanie i wylogowanie użytkownika.
 */
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    /**
     * Wyświetla formularz logowania i obsługuje błędy logowania.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    /**
     * Wylogowuje użytkownika (obsługiwane przez firewall).
     *
     * @return void
     */
    public function logout(): void
    {
        // Ta metoda jest przechwytywana przez firewall Symfony.
        // Nie powinna być wywoływana bezpośrednio.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

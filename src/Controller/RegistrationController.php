<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserRegistrationService $registrationService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $registrationService->register($user, $plainPassword);
            $registrationService->sendConfirmation($user);

            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRegistrationService $registrationService, UserRepository $userRepo): Response
    {
        $userId = $request->get('id');
        if (!$userId) {
            $this->addFlash('danger', 'Brak identyfikatora użytkownika.');
            return $this->redirectToRoute('app_login');
        }
        $user = $userRepo->find($userId);
        if ($user instanceof \App\Security\AuthUserDecorator) {
            $user = $user->getUser();
        }
        if (!$user) {
            $this->addFlash('danger', 'Nie znaleziono użytkownika.');
            return $this->redirectToRoute('app_login');
        }

        try {
            $registrationService->handleEmailConfirmation($request, $user);
            $this->addFlash('success', 'Adres e-mail został potwierdzony.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Nie udało się potwierdzić adresu e-mail.');
        }
        return $this->redirectToRoute('app_event_index');
    }
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('registration/check_email.html.twig');
    }
}

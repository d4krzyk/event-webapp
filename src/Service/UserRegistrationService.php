<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailVerifier $emailVerifier
    ) {}

    public function register(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function sendConfirmation(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('event-webapp@gmail.com', 'Event WebApp Mail Bot'))
            ->to((string) $user->getEmail())
            ->subject('Potwierdź swój adres e-mail')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);
    }
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
    }

}

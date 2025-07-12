<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Serwis obsługujący proces rejestracji użytkownika oraz potwierdzania adresu e-mail.
 */
class UserRegistrationService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var \App\Security\EmailVerifier
     */
    private $emailVerifier;

    /**
     * Konstruktor serwisu rejestracji użytkownika.
     *
     * @param EntityManagerInterface $em
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
     * @param \App\Security\EmailVerifier $emailVerifier
     */
    public function __construct(EntityManagerInterface $em, $passwordHasher, $emailVerifier)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Rejestruje nowego użytkownika i ustawia hasło.
     *
     * @param \App\Entity\User $user
     * @param string $plainPassword
     * @return void
     */
    public function register($user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Wysyła e-mail z linkiem potwierdzającym rejestrację.
     *
     * @param \App\Entity\User $user
     * @return void
     */
    public function sendConfirmation($user): void
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

    /**
     * Obsługuje potwierdzenie adresu e-mail użytkownika.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     * @return void
     */
    public function handleEmailConfirmation($request, $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
    }
}

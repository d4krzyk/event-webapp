<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Komponent dostarczający użytkowników do systemu bezpieczeństwa Symfony.
 *
 * Pozwala na ładowanie użytkownika po nazwie lub e-mailu, odświeżanie oraz sprawdzanie obsługiwanych klas.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @param UserRepository $userRepository Repozytorium użytkowników
     */
    public function __construct(private UserRepository $userRepository) {}

    /**
     * Ładuje użytkownika na podstawie identyfikatora (nazwa lub e-mail).
     *
     * @param string $identifier
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['username' => $identifier])
            ?? $this->userRepository->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new AuthUserDecorator($user);

    }

    /**
     * Odświeża dane użytkownika z bazy.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        $refreshedUser = $this->userRepository->find($user->getId());
        if (!$refreshedUser) {
            throw new UserNotFoundException();
        }
        return new AuthUserDecorator($refreshedUser);

    }

    /**
     * Sprawdza, czy provider obsługuje daną klasę użytkownika.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || AuthUserDecorator::class === $class;
    }
}

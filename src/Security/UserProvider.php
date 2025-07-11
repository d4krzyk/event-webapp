<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['username' => $identifier])
            ?? $this->userRepository->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new AuthUserDecorator($user);

    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $refreshedUser = $this->userRepository->find($user->getId());
        if (!$refreshedUser) {
            throw new UserNotFoundException();
        }
        return new AuthUserDecorator($refreshedUser);

    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || AuthUserDecorator::class === $class;
    }
}

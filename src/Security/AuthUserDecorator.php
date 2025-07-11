<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthUserDecorator implements UserInterface, PasswordAuthenticatedUserInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    public function getRoles(): array
    {
        return array_unique(array_merge($this->user->getRoles(), ['ROLE_AUTH_USER']));
    }

    public function getPassword(): ?string
    {
        return $this->user->getPassword();
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function eraseCredentials(): void
    {
        $this->user->eraseCredentials();
    }

    public function getId(): ?int
    {
        return $this->user->getId();
    }

}

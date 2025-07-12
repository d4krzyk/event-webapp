<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Dekorator użytkownika do rozszerzania funkcjonalności User w kontekście bezpieczeństwa.
 *
 * Pozwala na dodanie dodatkowych ról i metod pomocniczych bez modyfikowania oryginalnej encji User.
 */
class AuthUserDecorator implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var User Oryginalny użytkownik
     */
    private User $user;

    /**
     * Inicjalizuje dekorator użytkownika.
     *
     * @param User $user Owijany użytkownik
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Zwraca owijany obiekt użytkownika.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    /**
     * Zwraca e-mail użytkownika.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->user->getEmail();
    }
    /**
     * Zwraca nazwę użytkownika.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->user->getUsername();
    }
    /**
     * Zwraca role użytkownika wraz z dodatkową rolą 'ROLE_AUTH_USER'.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return array_unique(array_merge($this->user->getRoles(), ['ROLE_AUTH_USER']));
    }
    /**
     * Zwraca hasło użytkownika.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->user->getPassword();
    }
    /**
     * Zwraca identyfikator użytkownika do autoryzacji.
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->user->getUserIdentifier();
    }
    /**
     * Czyści tymczasowe dane wrażliwe użytkownika.
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        $this->user->eraseCredentials();
    }
    /**
     * Zwraca identyfikator użytkownika.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->user->getId();
    }
}

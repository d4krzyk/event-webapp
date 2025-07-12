<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
/**
 * Klasa encji reprezentująca użytkownika systemu.
 *
 * Przechowuje dane logowania, e-mail, role, status weryfikacji oraz powiązania z wydarzeniami, uczestnictwami i przypomnieniami.
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'createdByUser')]
    private Collection $events;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'participant')]
    private Collection $participations;

    /**
     * @var Collection<int, Reminder>
     */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'recipient')]
    private Collection $reminders;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;



    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->reminders = new ArrayCollection();
    }

    /**
     * Zwraca identyfikator użytkownika.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Zwraca identyfikator użytkownika do autoryzacji.
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Czyści tymczasowe dane wrażliwe użytkownika.
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        // Jeśli przechowujesz tymczasowe dane wrażliwe, wyczyść je tutaj
    }

    /**
     * Zwraca adres e-mail użytkownika.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Ustawia adres e-mail użytkownika.
     *
     * @param string $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Zwraca hasło użytkownika.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Ustawia hasło użytkownika.
     *
     * @param string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Zwraca role użytkownika.
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Ustawia role użytkownika.
     *
     * @param array $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Zwraca kolekcję wydarzeń utworzonych przez użytkownika.
     *
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * Dodaje wydarzenie do użytkownika.
     *
     * @param Event $event
     * @return static
     */
    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCreatedByUser($this);
        }

        return $this;
    }

    /**
     * Usuwa wydarzenie od użytkownika.
     *
     * @param Event $event
     * @return static
     */
    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCreatedByUser() === $this) {
                $event->setCreatedByUser(null);
            }
        }

        return $this;
    }

    /**
     * Zwraca kolekcję uczestnictw użytkownika.
     *
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    /**
     * Dodaje uczestnictwo do użytkownika.
     *
     * @param Participation $participation
     * @return static
     */
    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setParticipant($this);
        }

        return $this;
    }

    /**
     * Usuwa uczestnictwo od użytkownika.
     *
     * @param Participation $participation
     * @return static
     */
    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getParticipant() === $this) {
                $participation->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * Zwraca kolekcję przypomnień użytkownika.
     *
     * @return Collection<int, Reminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    /**
     * Dodaje przypomnienie do użytkownika.
     *
     * @param Reminder $reminder
     * @return static
     */
    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setRecipient($this);
        }

        return $this;
    }

    /**
     * Usuwa przypomnienie od użytkownika.
     *
     * @param Reminder $reminder
     * @return static
     */
    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            // set the owning side to null (unless already changed)
            if ($reminder->getRecipient() === $this) {
                $reminder->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * Zwraca nazwę użytkownika.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Ustawia nazwę użytkownika.
     *
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Sprawdza, czy użytkownik jest zweryfikowany.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Ustawia status weryfikacji użytkownika.
     *
     * @param bool $isVerified
     * @return static
     */
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}

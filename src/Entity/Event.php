<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Klasa encji reprezentująca wydarzenie.
 *
 * Przechowuje informacje o wydarzeniu, takie jak tytuł, opis, daty, lokalizacja, kategoria,
 * twórca oraz powiązane uczestnictwa i przypomnienia.
 */
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $endDate = null;

    /**
     * Sprawdza poprawność dat rozpoczęcia i zakończenia wydarzenia.
     *
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context Kontekst walidacji
     * @return void
     */
    #[Assert\Callback]
    public function validateDates(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            $context->buildViolation('Data rozpoczęcia nie może być późniejsza niż data zakończenia.')
                ->atPath('startDate')
                ->addViolation();
        }
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $createdByUser = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'event')]
    private Collection $participations;

    /**
     * @var Collection<int, Reminder>
     */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'event')]
    private Collection $reminders;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->reminders = new ArrayCollection();
    }

    /**
     * Zwraca identyfikator wydarzenia.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Zwraca tytuł wydarzenia.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Ustawia tytuł wydarzenia.
     *
     * @param string $title
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Zwraca opis wydarzenia.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Ustawia opis wydarzenia.
     *
     * @param string $description
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Zwraca datę rozpoczęcia wydarzenia.
     *
     * @return \DateTimeImmutable|null
     */
    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * Ustawia datę rozpoczęcia wydarzenia.
     *
     * @param \DateTimeImmutable $startDate
     * @return static
     */
    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Zwraca datę zakończenia wydarzenia.
     *
     * @return \DateTimeImmutable|null
     */
    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * Ustawia datę zakończenia wydarzenia.
     *
     * @param \DateTimeImmutable $endDate
     * @return static
     */
    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Zwraca użytkownika, który utworzył wydarzenie.
     *
     * @return User|null
     */
    public function getCreatedByUser(): ?User
    {
        return $this->createdByUser;
    }

    /**
     * Ustawia użytkownika, który utworzył wydarzenie.
     *
     * @param User|null $createdByUser
     * @return static
     */
    public function setCreatedByUser(?User $createdByUser): static
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    /**
     * Zwraca lokalizację wydarzenia.
     *
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * Ustawia lokalizację wydarzenia.
     *
     * @param Location|null $location
     * @return static
     */
    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Zwraca kategorię wydarzenia.
     *
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Ustawia kategorię wydarzenia.
     *
     * @param Category|null $category
     * @return static
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Zwraca kolekcję uczestnictw powiązanych z wydarzeniem.
     *
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    /**
     * Dodaje uczestnictwo do wydarzenia.
     *
     * @param Participation $participation
     * @return static
     */
    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setEvent($this);
        }

        return $this;
    }

    /**
     * Usuwa uczestnictwo z wydarzenia.
     *
     * @param Participation $participation
     * @return static
     */
    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getEvent() === $this) {
                $participation->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * Zwraca kolekcję przypomnień powiązanych z wydarzeniem.
     *
     * @return Collection<int, Reminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    /**
     * Dodaje przypomnienie do wydarzenia.
     *
     * @param Reminder $reminder
     * @return static
     */
    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setEvent($this);
        }

        return $this;
    }

    /**
     * Usuwa przypomnienie z wydarzenia.
     *
     * @param Reminder $reminder
     * @return static
     */
    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            // set the owning side to null (unless already changed)
            if ($reminder->getEvent() === $this) {
                $reminder->setEvent(null);
            }
        }

        return $this;
    }
}

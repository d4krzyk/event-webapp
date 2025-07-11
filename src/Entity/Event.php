<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[Assert\Callback]
    public function validateDates(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            $context->buildViolation('Data rozpoczęcia nie może być późniejsza niż data zakończenia.')
                ->atPath('startDate')
                ->addViolation();
        }
    }

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdByUser = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedByUser(): ?User
    {
        return $this->createdByUser;
    }

    public function setCreatedByUser(?User $createdByUser): static
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setEvent($this);
        }

        return $this;
    }

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
     * @return Collection<int, Reminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setEvent($this);
        }

        return $this;
    }

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

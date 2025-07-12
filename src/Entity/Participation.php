<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
/**
 * Klasa encji reprezentująca uczestnictwo użytkownika w wydarzeniu.
 *
 * Przechowuje informacje o powiązaniu użytkownika z wydarzeniem oraz dacie dołączenia.
 */
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $participant = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Event $event = null;

    /**
     * Zwraca identyfikator uczestnictwa.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Zwraca datę dołączenia do wydarzenia.
     *
     * @return \DateTimeImmutable|null
     */
    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    /**
     * Ustawia datę dołączenia do wydarzenia.
     *
     * @param \DateTimeImmutable $joinedAt
     * @return static
     */
    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    /**
     * Zwraca użytkownika uczestniczącego w wydarzeniu.
     *
     * @return User|null
     */
    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    /**
     * Ustawia użytkownika uczestniczącego w wydarzeniu.
     *
     * @param User|null $participant
     * @return static
     */
    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Zwraca wydarzenie, w którym uczestniczy użytkownik.
     *
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * Ustawia wydarzenie, w którym uczestniczy użytkownik.
     *
     * @param Event|null $event
     * @return static
     */
    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ReminderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReminderRepository::class)]
/**
 * Klasa encji reprezentująca przypomnienie o wydarzeniu.
 *
 * Przechowuje informacje o dacie przypomnienia, statusie wysłania, odbiorcy i powiązanym wydarzeniu.
 */
class Reminder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $remindAt = null;

    #[ORM\Column]
    private ?bool $sent = null;

    #[ORM\ManyToOne(inversedBy: 'reminders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\ManyToOne(inversedBy: 'reminders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Event $event = null;

    /**
     * Zwraca identyfikator przypomnienia.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Zwraca datę i godzinę przypomnienia.
     *
     * @return \DateTimeImmutable|null
     */
    public function getRemindAt(): ?\DateTimeImmutable
    {
        return $this->remindAt;
    }

    /**
     * Ustawia datę i godzinę przypomnienia.
     *
     * @param \DateTimeImmutable $remindAt
     * @return static
     */
    public function setRemindAt(\DateTimeImmutable $remindAt): static
    {
        $this->remindAt = $remindAt;

        return $this;
    }

    /**
     * Sprawdza, czy przypomnienie zostało wysłane.
     *
     * @return bool|null
     */
    public function isSent(): ?bool
    {
        return $this->sent;
    }

    /**
     * Ustawia status wysłania przypomnienia.
     *
     * @param bool $sent
     * @return static
     */
    public function setSent(bool $sent): static
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Zwraca użytkownika, który jest odbiorcą przypomnienia.
     *
     * @return User|null
     */
    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    /**
     * Ustawia odbiorcę przypomnienia.
     *
     * @param User|null $recipient
     * @return static
     */
    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Zwraca wydarzenie powiązane z przypomnieniem.
     *
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * Ustawia wydarzenie powiązane z przypomnieniem.
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

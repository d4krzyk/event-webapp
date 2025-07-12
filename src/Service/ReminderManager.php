<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Reminder;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Serwis do zarządzania przypomnieniami o wydarzeniach.
 *
 * Pozwala tworzyć i usuwać przypomnienia powiązane z użytkownikiem i wydarzeniem.
 */
class ReminderManager
{
    /**
     * @param EntityManagerInterface $em Menedżer encji Doctrine
     */
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Tworzy nowe przypomnienie dla użytkownika o danym wydarzeniu.
     *
     * @param User $user Użytkownik, który otrzyma przypomnienie
     * @param Event $event Wydarzenie, którego dotyczy przypomnienie
     * @param \DateTimeImmutable $remindAt Data i godzina przypomnienia
     * @return Reminder Nowo utworzone przypomnienie
     */
    public function createReminder(User $user, Event $event, \DateTimeImmutable $remindAt): Reminder
    {
        $reminder = new Reminder();
        $reminder->setRecipient($user);
        $reminder->setEvent($event);
        $reminder->setRemindAt($remindAt);
        $reminder->setSent(false);

        $this->em->persist($reminder);

        return $reminder;
    }

    /**
     * Usuwa przypomnienie powiązane z użytkownikiem i wydarzeniem.
     *
     * @param User $user Użytkownik
     * @param Event $event Wydarzenie
     * @return void
     */
    public function removeReminder(User $user, Event $event): void
    {
        $reminder = $this->em->getRepository(Reminder::class)
            ->findOneBy(['recipient' => $user, 'event' => $event]);
        if ($reminder) {
            $this->em->remove($reminder);
        }
    }
}

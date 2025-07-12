<?php

namespace App\Command;

use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'reminder:send',
    description: 'Wysyła maile z przypomnieniami o wydarzeniach, gdy nadejdzie czas przypomnienia.'
)]
/**
 * Komenda konsolowa wysyłająca e-maile z przypomnieniami o wydarzeniach,
 * gdy nadejdzie czas przypomnienia.
 */
class SendEventRemindersCommand extends Command
{
    /**
     * @param ReminderRepository $reminderRepo Repozytorium przypomnień
     * @param EntityManagerInterface $em Menedżer encji Doctrine
     * @param MailerInterface $mailer Komponent do wysyłania e-maili
     */
    public function __construct(
        private ReminderRepository $reminderRepo,
        private EntityManagerInterface $em,
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    /**
     * Wykonuje logikę wysyłania przypomnień e-mail.
     *
     * @param InputInterface $input Wejście konsoli
     * @param OutputInterface $output Wyjście konsoli
     * @return int Kod zakończenia
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();

        $reminders = $this->reminderRepo->createQueryBuilder('r')
            ->join('r.event', 'e')
            ->join('r.recipient', 'u')
            ->where('r.remindAt <= :now')
            ->andWhere('r.sent = false')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        foreach ($reminders as $reminder) {
            $user = $reminder->getRecipient();
            $event = $reminder->getEvent();
            $formatter = new \IntlDateFormatter(
                'pl_PL',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::SHORT,
                $event->getStartDate()->getTimezone(),
                \IntlDateFormatter::GREGORIAN,
                "d MMMM yyyy 'o godzinie' HH:mm"
            );
            $dataWydarzenia = $formatter->format($event->getStartDate());
            $html = '
            <div style="font-family: Arial, sans-serif; background: #f8f9fa; padding: 24px;">
                <div style="background: #fff; border-radius: 8px; padding: 24px; max-width: 500px; margin: auto; box-shadow: 0 2px 8px #e9ecef;">
                    <h2 style="color: #0d6efd; margin-top: 0; margin-bottom: 18px;">Przypomnienie o wydarzeniu</h2>
                    <div style="font-size: 16px; color: #212529;">
                        <div style="font-weight: bold; margin-bottom: 10px;">' . htmlspecialchars($event->getTitle()) . '</div>
                        <div style="color: #0e171e; margin-bottom: 10px;">
                            <strong>Opis:</strong> ' . nl2br(htmlspecialchars($event->getDescription())) . '
                        </div>
                        <div style="color: #22242a; margin-bottom: 10px;">
                            <strong>Miejsce:</strong><br>
                            ' . htmlspecialchars($event->getLocation()->getName()) . '<br>' .
                                        htmlspecialchars($event->getLocation()->getAddress()) . '<br>' .
                                        htmlspecialchars($event->getLocation()->getCity()) . '
                        </div>
                        <div style="margin-bottom: 0;">Które odbędzie się <strong>' . $dataWydarzenia . '</strong>.</div>
                    </div>
                    <p style="font-size: 14px; color: #6c757d; margin-top: 24px; margin-bottom: 0;">
                        Dziękujemy za korzystanie z naszego serwisu!
                    </p>
                </div>
            </div>
            ';
            $email = (new Email())
                ->from('noreply@przypomnienie-event-webapp.pl')
                ->to($user->getEmail())
                ->subject('Przypomnienie o wydarzeniu: ' . $event->getTitle())
                ->html($html);


            $this->mailer->send($email);

            $reminder->setSent(true);
        }

        $this->em->flush();

        $output->writeln('Wysłano przypomnienia.');
        return Command::SUCCESS;
    }
}

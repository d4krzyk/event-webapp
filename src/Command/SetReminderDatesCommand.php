<?php

namespace App\Command;

use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:set-reminder-dates',
    description: 'Ustawia datę przypomnienia na 2 dni przed eventem dla wszystkich reminderów, gdzie jeszcze nie ustawiono daty.'
)]
/**
 * Komenda konsolowa ustawiająca daty przypomnień na 2 dni przed wydarzeniem,
 * dla wszystkich przypomnień, które nie mają jeszcze ustawionej daty,
 * a wydarzenie rozpoczyna się za co najmniej 5 dni.
 */
class SetReminderDatesCommand extends Command
{
    /**
     * @param ReminderRepository $reminderRepo Repozytorium przypomnień
     * @param EntityManagerInterface $em Menedżer encji Doctrine
     */
    public function __construct(
        private ReminderRepository     $reminderRepo,
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    /**
     * Wykonuje logikę ustawiania dat przypomnień.
     *
     * @param InputInterface $input Wejście konsoli
     * @param OutputInterface $output Wyjście konsoli
     * @return int Kod zakończenia
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();
        $fiveDaysLater = $now->modify('+5 days');

        $reminders = $this->reminderRepo->createQueryBuilder('r')
            ->join('r.event', 'e')
            ->where('r.remindAt IS NULL')
            ->andWhere('e.startDate >= :fiveDaysLater')
            ->setParameter('fiveDaysLater', $fiveDaysLater)
            ->getQuery()
            ->getResult();

        foreach ($reminders as $reminder) {
            $eventDate = $reminder->getEvent()->getStartDate();
            $remindAt = (clone $eventDate)->modify('-2 days');
            $reminder->setRemindAt($remindAt);
        }

        $this->em->flush();

        $output->writeln('Daty przypomnień ustawione.');
        return Command::SUCCESS;
    }
}

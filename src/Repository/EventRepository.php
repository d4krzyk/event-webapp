<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repozytorium obsługujące zapytania dotyczące wydarzeń.
 *
 * @extends ServiceEntityRepository<Event>
 *
 * Dostarcza metody do pobierania, filtrowania i sortowania wydarzeń.
 */
class EventRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry Rejestr menedżera Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Wyszukuje wydarzenia na podstawie przekazanych filtrów.
     *
     * @param array $filters Tablica filtrów (tytuł, kategoria, lokalizacja, daty, status, sortowanie)
     * @return Event[]
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('e');
        if (!empty($filters['title'])) {
            $qb->andWhere('e.title LIKE :title')
                ->setParameter('title', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['category'])) {
            $qb->andWhere('e.category = :category')
                ->setParameter('category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $qb->andWhere('e.location = :location')
                ->setParameter('location', $filters['location']);
        }
        if (!empty($filters['startDate'])) {
            $qb->andWhere('e.startDate >= :startDate')
                ->setParameter('startDate', $filters['startDate']);
        }
        if (!empty($filters['endDate'])) {
            $qb->andWhere('e.endDate <= :endDate')
                ->setParameter('endDate', $filters['endDate']);
        }
        if (!empty($filters['status'])) {
            $now = new \DateTimeImmutable();
            if ($filters['status'] === 'ongoing') {
                $qb->andWhere('e.startDate <= :now AND e.endDate >= :now')
                    ->setParameter('now', $now);
            } elseif ($filters['status'] === 'upcoming') {
                $qb->andWhere('e.startDate > :now')
                    ->setParameter('now', $now);
            } elseif ($filters['status'] === 'finished') {
                $qb->andWhere('e.endDate < :now')
                    ->setParameter('now', $now);
            }
        }

        if (!empty($filters['sortBy']) && $filters['sortBy'] === 'popularity') {
            $qb->leftJoin('e.participations', 'p')
                ->addSelect('COUNT(p.id) AS HIDDEN popularity')
                ->groupBy('e.id');
            $qb->orderBy('popularity', $filters['sortOrder'] ?? 'DESC');
        } elseif (!empty($filters['sortBy'])) {
            $qb->orderBy('e.' . $filters['sortBy'], $filters['sortOrder'] ?? 'ASC');
        } else {
            $qb->orderBy('e.startDate', 'ASC');
        }
        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

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

<?php

namespace App\Repository;

use App\Entity\DowntimeInterventionDuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DowntimeInterventionDuration>
 *
 * @method DowntimeInterventionDuration|null find($id, $lockMode = null, $lockVersion = null)
 * @method DowntimeInterventionDuration|null findOneBy(array $criteria, array $orderBy = null)
 * @method DowntimeInterventionDuration[]    findAll()
 * @method DowntimeInterventionDuration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DowntimeInterventionDurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DowntimeInterventionDuration::class);
    }

//    /**
//     * @return DowntimeInterventionDuration[] Returns an array of DowntimeInterventionDuration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DowntimeInterventionDuration
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

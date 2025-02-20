<?php

namespace App\Repository;

use App\Entity\InterventionDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterventionDescription>
 *
 * @method InterventionDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterventionDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterventionDescription[]    findAll()
 * @method InterventionDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterventionDescription::class);
    }

//    /**
//     * @return InterventionDescription[] Returns an array of InterventionDescription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InterventionDescription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

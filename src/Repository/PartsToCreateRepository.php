<?php

namespace App\Repository;

use App\Entity\PartsToCreate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartsToCreate>
 *
 * @method PartsToCreate|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartsToCreate|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartsToCreate[]    findAll()
 * @method PartsToCreate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartsToCreateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartsToCreate::class);
    }

//    /**
//     * @return PartsToCreate[] Returns an array of PartsToCreate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PartsToCreate
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

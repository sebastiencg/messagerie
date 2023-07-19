<?php

namespace App\Repository;

use App\Entity\Validity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Validity>
 *
 * @method Validity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Validity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Validity[]    findAll()
 * @method Validity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Validity::class);
    }

//    /**
//     * @return Validity[] Returns an array of Validity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Validity
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

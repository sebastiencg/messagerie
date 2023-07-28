<?php

namespace App\Repository;

use App\Entity\Friend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friend>
 *
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friend::class);
    }
    public function custom1($value1, $value2): array
    {
        return $this->createQueryBuilder('friend')
            ->andWhere('friend.ofUser1 = :key1 AND friend.ofUser2 = :key2 OR friend.ofUser2 = :key1 AND friend.ofUser1 = :key2')
            ->setParameter('key1', $value1)
            ->setParameter('key2', $value2)
            ->orderBy('friend.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function custom2($value1): array
    {
        return $this->createQueryBuilder('friend')
            ->andWhere('friend.ofUser1 = :key1 OR friend.ofUser2 = :key1')
            ->setParameter('key1', $value1)
            ->orderBy('friend.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Friend[] Returns an array of Friend objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Friend
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

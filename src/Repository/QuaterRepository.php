<?php

namespace App\Repository;

use App\Entity\Quater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quater|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quater|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quater[]    findAll()
 * @method Quater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quater::class);
    }

    // /**
    //  * @return Quater[] Returns an array of Quater objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quater
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

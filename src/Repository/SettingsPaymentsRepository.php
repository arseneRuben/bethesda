<?php

namespace App\Repository;

use App\Entity\SettingsPayments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsPayments|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsPayments|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsPayments[]    findAll()
 * @method SettingsPayments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsPaymentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsPayments::class);
    }

    // /**
    //  * @return SettingsPayments[] Returns an array of SettingsPayments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SettingsPayments
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

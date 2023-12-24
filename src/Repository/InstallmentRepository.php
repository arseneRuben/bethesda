<?php

namespace App\Repository;

use App\Entity\Installment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Installment>
 *
 * @method Installment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Installment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Installment[]    findAll()
 * @method Installment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstallmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Installment::class);
    }

//    /**
//     * @return Installment[] Returns an array of Installment objects
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

//    public function findOneBySomeField($value): ?Installment
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

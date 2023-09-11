<?php

namespace App\Repository;

use App\Entity\AbscenceSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbscenceSheet>
 *
 * @method AbscenceSheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbscenceSheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbscenceSheet[]    findAll()
 * @method AbscenceSheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbscenceSheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbscenceSheet::class);
    }

//    /**
//     * @return AbscenceSheet[] Returns an array of AbscenceSheet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AbscenceSheet
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

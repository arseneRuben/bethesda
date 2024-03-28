<?php

namespace App\Repository;

use App\Entity\MainTeacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainTeacher>
 *
 * @method MainTeacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainTeacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainTeacher[]    findAll()
 * @method MainTeacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainTeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainTeacher::class);
    }

//    /**
//     * @return MainTeacher[] Returns an array of MainTeacher objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MainTeacher
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

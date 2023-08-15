<?php

namespace App\Repository;

use App\Entity\Quater;
use App\Entity\SchoolYear;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function findQuaterThisYear( SchoolYear $year) {
       
        $qb = $this->createQueryBuilder('q')
                 ->leftJoin('q.schoolYear', 'y')
                 ->where('y.id=:year')
               
                 ->setParameter('year', $year->getId());
        return $qb->getQuery()->getResult();          
}
    
public function findActivatedQuaterThisYear(SchoolYear $year)
{
    $qb = $this->createQueryBuilder('q')
        ->leftJoin('q.schoolYear', 'y')
        ->where('y = :year')
        ->andWhere('q.activated = true')
        ->setParameter('year', $year);

    return $qb->getQuery()->getResult();
}

}

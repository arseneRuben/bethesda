<?php

namespace App\Repository;

use App\Entity\Sequence;
use App\Entity\SchoolYear;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Sequence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sequence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sequence[]    findAll()
 * @method Sequence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sequence::class);
    }

    // /**
    //  * @return Sequence[] Returns an array of Sequence objects
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
    public function findOneBySomeField($value): ?Sequence
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findSequenceThisYear( SchoolYear $year) {
       
        $qb = $this->createQueryBuilder('s')
                 ->leftJoin('s.quater', 'q')
                 ->leftJoin('q.schoolYear', 'y')
                 ->where('y.id=:year')
               
                 ->setParameter('year', $year->getId());
        return $qb->getQuery()->getResult();          
}

public function findActivatedSequenceThisYear(SchoolYear $year)
{
    $qb = $this->createQueryBuilder('s')
        ->leftJoin('s.quater', 'q')
        ->leftJoin('q.schoolYear', 'y')
        ->where('y = :year')
        ->andWhere('s.activated = true')
        ->setParameter('year', $year);

    return $qb->getQuery()->getResult();
}
}

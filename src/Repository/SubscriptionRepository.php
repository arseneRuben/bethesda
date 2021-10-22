<?php

namespace App\Repository;
use App\Entity\ClassRoom;
use App\Entity\Student;
use App\Entity\SchoolYear;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findNotEnrolledStudents3ThisYear(ClassRoom $room, SchoolYear $year) {
      
        return $this->createQueryBuilder('s')
                         ->leftJoin('s.schoolYear', 'sc')
                         ->leftJoin('s.classRoom', 'cl')
                         ->where('sc.id=:year')
                         ->where('cl.id=:room')
                         ->setParameter('year', $year->getId())
                         ->setParameter('room', $room->getId());
                         
     }
     public function findEnrollementThisYear( SchoolYear $year) {
       
                 $qb = $this->createQueryBuilder('s')
                          ->leftJoin('s.schoolYear', 'sc')
                          ->where('sc.id=:year')
                          ->addOrderBy('s.classRoom', 'ASC')
                          ->setParameter('year', $year->getId());
                 return $qb->getQuery()->getResult();          
      }

      public function findByYear_Room( SchoolYear $year, ClassRoom $room) {
       
        $qb = $this->createQueryBuilder('s')
                 ->leftJoin('s.schoolYear', 'sc')
                 ->where('sc.id=:year')
                 ->leftJoin('s.classRoom', 'cl')
                 ->where('cl.id=:room')
                
                 ->setParameter('year', $year->getId())
                 ->setParameter('room', $room->getId());
        return $qb->getQuery()->getResult();          
}

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
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
    public function findOneBySomeField($value): ?Subscription
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

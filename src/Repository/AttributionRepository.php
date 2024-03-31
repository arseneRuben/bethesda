<?php

namespace App\Repository;

use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Entity\Attribution;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribution::class);
    }


    public function findNotAttributedCoursesThisYear(SchoolYear $year) {
        $query = $this->getEntityManager()
                ->createQuery(
                        "SELECT crse
                             FROM  App\Entity\Course  crse
                             JOIN App\Entity\Attribution att WITH  att.course  =  crse.id
                             JOIN App\Entity\SchoolYear schoolYear  WITH  att.schoolYear     =  schoolYear.id
                             WHERE att.schoolYear = :year
                             AND crse.attributed =:attributed 
                            "
                )->setParameter('year', $year)
                ->setParameter('attributed', false)
         ;

        return $query->getResult();
    }

    public function findAllThisYear(SchoolYear $year) {
      
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.schoolYear', 'sc')
            ->leftJoin('a.teacher', 't')
                ->where('sc.id=:year')
                ->orderBy('t.fullName')
                ->setParameter('year', $year->getId());
              
        return $qb->getQuery()->getResult();
    }

    public function findByYearAndByRoom(SchoolYear $year, ClassRoom $room) {
      
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.schoolYear', 'sc')
            ->leftJoin('a.course', 'c')
            ->leftJoin('c.module', 'm')
            ->leftJoin('m.room', 'r')
                ->where('sc.id=:year')
                ->andWhere('r.id=:room')
                ->setParameter('room', $room->getId())
                ->setParameter('year', $year->getId());
              
        return $qb->getQuery()->getResult();
    }


}

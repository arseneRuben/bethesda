<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\ClassRoom;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.room.id', 'ASC')
            ->orderBy('c.module', 'ASC')

            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByClassRoom(int $idClass)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT c.code, c.wording, c.coefficient
                             FROM  App\Entity\ClassRoom  room
                             JOIN  App\Entity\Module mod WITH  room.modules  =  mod.id
                             JOIN App\Entity\Course c  WITH  c.module     =  mod.id
                             WHERE room.id = :idClass
                             GROUP BY mod.id
                            "
            )->setParameter('idClass', $idClass);

        return $query->getResult();
    }

    public function findProgrammedCoursesInClass(ClassRoom $room)
    {
        $qb = $this->createQueryBuilder('crs')
            ->leftJoin('crs.module', 'm')
            ->leftJoin('m.room', 'rm')
            ->andWhere('rm.id=:room')
            ->setParameter('room', $room->getId());
        return $qb->getQuery()->getResult();
    }

    public function findNotAttributedCoursesAtActivatedYear()
    {

        $subQueryBuilder  = $this->getEntityManager()->createQueryBuilder();
        $subQuery = $subQueryBuilder
            ->select(['crs.id'])
            ->from('App\Entity\Course', 'crs')
            ->innerJoin('crs.attributions', 'attr')
            ->innerJoin('attr.schoolYear', 'sc')
            ->andWhere('sc.activated=:v')
            ->setParameter('v', true)
            ->getQuery()
            ->getArrayResult();


        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query = $queryBuilder
            ->select(['crs'])
            ->from('App\Entity\Course', 'crs')
            ->where($queryBuilder->expr()->notIn('crs.id', ':subQuery'))

            ->orderBy('crs.domain')
            ->setParameter('subQuery', $subQuery);
        dd($query->getQuery()->getSQL());
        return $query;
    }
}

<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SchoolYearRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    private $scRepo;
    public function __construct(ManagerRegistry $registry, SchoolYearRepository $scRepo)
    {
        parent::__construct($registry, Student::class);
        $this->scRepo = $scRepo;
    }

   


    // /**
    //  * @return Student[] Returns an array of Student objects
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
    public function findOneBySomeField($value): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getNumeroDispo()
    {
        $date = new \DateTime;
        $debutNum = $date->format('Ymd');
        $qb = $this->createQueryBuilder('s')
                ->add('select', '(s.matricule)as matricule');
        $qb->where($qb->expr()->like('s.matricule', ':matricule'))
                ->setParameter('matricule', '%' . $debutNum . '%')
                ->orderBy('s.matricule', 'DESC')
                ->setMaxResults(1);

        $matricule = $qb->getQuery()
                ->getResult();
        if (count($matricule) >> 0) {
            return $matricule[0]['matricule'] + 1;
        } else {
            return $debutNum . "0001";
        }
    }

    public function findEnrolledStudentsThisYear(ClassRoom $room, int $year)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                    "SELECT std.matricule,std.id, std.lastname, std.firstname, std.gender, std.birthplace, std.birthday , std.primaryContact, std.secondaryContact
                             FROM  Student  std
                             JOIN  Subscription sub WITH  sub.student  =  std.id
                             JOIN  SchoolYear schoolYear  WITH  sub.schoolYear     =  schoolYear.id
                             WHERE sub.schoolYear = :year
                             AND  sub.classRoom = :room 
                             ORDER BY std.lastname
                            "
                )->setParameter('year', $year)
                ->setParameter('room', $room->getId());
 //               ->setFirstResult(26) ;

        return $query->getResult();
    }

    public function findEnrolledStudents2ThisYear(ClassRoom $room, int $year)
    {
        //SELECT matricule,firstname, lastname, gender FROM  student  std JOIN inscription sub ON  sub.student_id  =  std.id JOIN school_year sc ON  sub.year_id =  sc.id  WHERE sub.year_id = :year AND  sub.classroom_id = :room

        $query = $this->getEntityManager()
                ->createQuery(
                    "SELECT std
                             FROM  Student  std
                             JOIN  Subscription sub ON  sub.student  =  std.id
                             JOIN  SchoolYear schoolYear  WITH  sub.schoolYear     =  schoolYear.id
                             WHERE sub.schoolYear = :year
                             AND  sub.classRoom = :room 
                            "
                )->setParameter('year', $year)
                ->setParameter('room', $room->getId());

        return $query->getResult();
    }

    public function findNotEnrolledStudents2ThisYear(int $year)
    {
        //SELECT matricule,firstname, lastname, gender FROM  student  std JOIN inscription sub ON  sub.student_id  =  std.id JOIN school_year sc ON  sub.year_id =  sc.id  WHERE sub.year_id = :year AND  sub.classroom_id = :room
        
        $query = $this->getEntityManager()
                ->createQuery(
                    " SELECT st 
                               FROM    Student  st
                                WHERE st.matricule not in 
                                (SELECT std.matricule
                             FROM  Student  std, Subscription sub, SchoolYear yr 
                              WHERE  sub.student  =  std.id AND sub.schoolYear   =  yr.id AND sub.schoolYear = :year)   
                            "
                )->setParameter('year', $year)
               ;
        return $query->getResult();
    }
    public function findNotEnrolledStudents2ThisYearRoom(ClassRoom $room, int $year)
    {
        //SELECT matricule,firstname, lastname, gender FROM  student  std JOIN inscription sub ON  sub.student_id  =  std.id JOIN school_year sc ON  sub.year_id =  sc.id  WHERE sub.year_id = :year AND  sub.classroom_id = :room

        $query = $this->getEntityManager()
                ->createQuery(
                    " SELECT std 
                               FROM    Student  std
                                EXCEPT
                                SELECT std
                             FROM Student  std
                             JOIN Subscription sub ON  sub.student  =  std.id
                             JOIN SchoolYear schoolYear  WITH  sub.schoolYear     =  schoolYear.id
                             WHERE sub.schoolYear = :year
                             AND  sub.classRoom = :room 
                            "
                )->setParameter('year', $year)
                ->setParameter('room', $room->getId());

        return $query->getResult();
    }

    public function findEnrolledStudentsThisYearInClass(ClassRoom $room, SchoolYear $year)
    {
        $qb = $this->createQueryBuilder('s')
                ->leftJoin('s.subscriptions', 'sub')
                ->leftJoin('sub.classRoom', 'cl')
                ->where('sub.schoolYear=:year')
                ->andWhere('cl.id=:room')
                ->orderBy('s.lastname')
                ->setParameter('year', $year->getId())
                ->setParameter('room', $room->getId());
        return $qb->getQuery()->getResult();
    }


    public function findEnrolledStudentsThisYear2()
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
       
        $query = $this->getEntityManager()
                ->createQuery(
                             " SELECT st 
                               FROM   App\Entity\Student  st
                               WHERE st.matricule not in 
                               (SELECT std.matricule
                                FROM App\Entity\Student  std, App\Entity\Subscription sub, App\Entity\SchoolYear yr 
                              WHERE  sub.student  =  std.id AND sub.schoolYear   =  yr.id AND sub.schoolYear = :year)   
                              ORDER BY  st.lastname
                            "
                )->setParameter('year', $year->getId())
               ;
             
        return $query->getResult();
    }

    public function findNotEnrolledStudentsThisYear(SchoolYear $year)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                    "SELECT std
                             FROM  Student  std
                             JOIN Subscription sub WITH  sub.student  =  std.id
                             JOIN SchoolYear schoolYear  WITH  sub.schoolYear     =  schoolYear.id
                             WHERE sub.schoolYear = :year
                             AND std.enrolled =:enrolled 
                            "
                )->setParameter('year', $year)
                ->setParameter('enrolled', false);

        return $query->getResult();
    }

    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createDefaultQueryBuilder()
    {
        return $this->createQueryBuilder('e');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->createGetByIdQueryBuilder($id)->getQuery()->getSingleResult();
    }

  
    public function createGetByIdQueryBuilder($id)
    {
        return $this->createDefaultQueryBuilder()
                        ->where('e.id =: id')
                        ->setParameter('id', $id);
    }
}

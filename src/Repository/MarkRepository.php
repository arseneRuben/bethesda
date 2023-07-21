<?php

namespace App\Repository;

use App\Entity\Mark;
use App\Entity\Course;
use App\Entity\Sequence;
use App\Entity\ClassRoom;
use App\Entity\Subscription;
use App\Entity\SchoolYear;
use App\Entity\Student;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Mark|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mark|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mark[]    findAll()
 * @method Mark[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mark::class);
    }

    public function findMarkBySequenceAndCourseOderByStd(Sequence $sequence, Course $course)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.student', 'std')
            ->leftJoin('m.evaluation', 'eval')
            ->leftJoin('eval.sequence', 'seq')
            ->where('eval.sequence=:sequence')
            ->andWhere('eval.course=:course')
            ->orderBy('std.lastname')
            ->setParameter('sequence', $sequence->getId())
            ->setParameter('course', $course->getId());
        return $qb->getQuery()->getResult();
    }

    public function findMarksBySequence_Class_StudentOrderByStd(Sequence $sequence, ClassRoom $room, Student $std)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.student', 'std')
            ->leftJoin('m.evaluation', 'eval')
            ->leftJoin('eval.sequence', 'seq')
            ->where('eval.sequence=:sequence')
            ->andWhere('eval.classRoom=:room')
            ->andWhere('m.student=:student')
            ->setParameter('sequence', $sequence->getId())
            ->setParameter('room', $room->getId())
            ->setParameter('student', $std->getId());
        return $qb->getQuery()->getResult();
    }
    public function findMarksBySequenceAndClassOrderByStd(Sequence $sequence, ClassRoom $room)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.evaluation', 'eval')
            ->leftJoin('m.student', 'std')
            ->where('eval.moyenne>0')
            ->where('eval.sequence=:sequence')
            ->andWhere('eval.classRoom=:room')
            ->orderBy('std.lastname')
            ->setParameter('sequence', $sequence->getId())
            ->setParameter('room', $room->getId());
        return $qb->getQuery()->getResult();
    }

    public function findMarksBySequenceAndClassOrderByStd3(Sequence $sequence, ClassRoom $room)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT std.matricule as matricule, std.profileImagePath as profileImagePath,  std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender,
                             std.birthplace as birthplace  , mod.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.appreciation as appreciation
                             FROM  App:Mark  m
                             JOIN  App:Student    std    WITH  m.student        =  std.id
                            JOIN  App:Evaluation eval   WITH  m.evaluation     =  eval.id
                            JOIN  App:Course     crs    WITH  eval.course      =  crs.id
                             JOIN  App:Module     mod    WITH  mod.id           =  crs.module
                             JOIN  App:Sequence   seq    WITH  seq.id           =  eval.sequence
                             WHERE  eval.sequence = :sequence
                             AND  eval.classRoom = :room
                             ORDER BY std.lastname, crs.module, crs.wording"
            )->setParameter('sequence', $sequence->getId())
            ->setParameter('room', $room->getId());
        return $query->getResult();
    }

    public function findMarksBySequenceAndClassOrderByStd2(Sequence $sequence, ClassRoom $room)
    {
        $year = $sequence->getQuater()->getSchoolYear();
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT std.matricule as matricule,   std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender, eval.competence as competence,
                             std.birthplace as birthplace   , teach.fullName as teacher    , mod.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.rank2 as rank, m.appreciation as appreciation
                             FROM  App:Mark  m
                             JOIN  App:Student    std    WITH  m.student        =   std.id
                             JOIN  App:Evaluation eval   WITH  m.evaluation     =   eval.id
                             JOIN  App:Course      crs   WITH  eval.course      =   crs.id
                             JOIN  App:Attribution att   WITH  att.course       =   crs.id
                             JOIN  App:User      teach   WITH  att.teacher      =   teach.id
                             JOIN  App:Module     mod    WITH  mod.id           =   crs.module
                             JOIN  App:Sequence   seq    WITH  seq.id           =   eval.sequence
                             WHERE  eval.sequence = :sequence
                             AND att.schoolYear = :year
                             AND  eval.classRoom = :room
                             ORDER BY std.lastname, module, crs.wording"
            )->setParameter('sequence', $sequence->getId())
            ->setParameter('room', $room->getId())
            ->setParameter('year', $year->getId())
            //  ->setFirstResult(150) ->setMaxResults(550)
        ;
        return $query->getResult();
    }

    public function findMarksByYearAndClassOrderByStd(ClassRoom $room, SchoolYear $year)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT year, room,  matricule,    lastname,  firstname,  birthday,  gender,
                             birthplace   ,  teacher    ,  module ,  wording,  coefficient,valeur1, valeur2, valeur3,valeur
                             FROM   data
                             WHERE  year =:year
                             AND  room =:room
                             ORDER BY year, room, lastname, module, wording"
            )->setParameter('year', $year->getId())
            ->setParameter('room', $room->getId());
        return $query->getResult();
    }

    public function findMarksByRoomAndYearOrderBySeq(SchoolYear $year, ClassRoom $room)
    {
        $this->updateView($year, $room);
        $em = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($em);
        $query = $em
            ->createQuery(
                "   SELECT course, module, sequence, coefficient, weight , valeur FROM   V_GRAPH_MARK_" . $room->getName() . "    
                    WHERE room =:room
                    ORDER BY  sequence, module"
            )->setParameter('room', $room->getId());
        return $query->getResult();
    }

    public function findMarksByMatriculeAndYearOrderBySeq($matricule, SchoolYear $year, ClassRoom $room)
    {
        $this->updateView($year, $room);
        $em = $this->getEntityManager();


        /*  $query = $this->getEntityManager()
            ->createQuery(
                "   SELECT course, module, sequence, coefficient, weight , valeur FROM   V_GRAPH_MARK_".$room->getName()."    
                    WHERE matricule =:matricule
                    ORDER BY  sequence, module"
            )->setParameter('matricule', $matricule);
        return $query->getResult();*/
    }

    public function updateView(SchoolYear $year, ClassRoom $room)
    {


        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "  CREATE OR REPLACE ALGORITHM=TEMPTABLE VIEW V_GRAPH_MARK_" . $room->getId() . " AS
        SELECT DISTINCT   std.matricule as matricule,  crs.code  as course, module.code  as module, seq.id as sequence,
         crs.coefficient as coefficient,m.value as valeur, m.weight as weight,  m.id as id1
        FROM      sequence   seq
        LEFT JOIN      quater     quat    ON  seq.quater_id     =   quat.id   
        LEFT JOIN      evaluation eval    ON  eval.sequence_id     =   seq.id 
        LEFT JOIN      mark        m      ON  m.evaluation_id     =   eval.id
        LEFT JOIN      course     crs     ON  eval.course_id      =   crs.id
        LEFT JOIN      module     module  ON  crs.module_id       =   module.id
        LEFT JOIN      student    std     ON  m.student_id        =   std.id
        WHERE quat.school_year_id  =   :year AND  eval.classroom_id   =   :room
        ORDER BY  sequence, module; ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array('room' => $room->getId(), 'year' => $year->getId()));
        //$query->getResult();
    }

    public function findMarksBySequenceAndSubscription(Sequence $sequence, Subscription $sub)
    {
        $year = $sequence->getQuater()->getSchoolYear();

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT std.matricule as matricule,   std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender, eval.competence as competence,
                             std.birthplace as birthplace   , teach.fullName as teacher    , mod.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.rank2 as rank, m.appreciation as appreciation
                             FROM  App:Mark  m
                             JOIN  App:Student    std    WITH  m.student        =   std.id
                             JOIN  App:Evaluation eval   WITH  m.evaluation     =   eval.id
                             JOIN  App:Course      crs   WITH  eval.course      =   crs.id
                             JOIN  App:Module     mod    WITH  mod.id           =   crs.module
                             JOIN  App:Sequence   seq    WITH  seq.id           =   eval.sequence
                             JOIN  App:Attribution att   WITH  att.course       =   crs.id
                             JOIN  App:User      teach   WITH  att.teacher      =   teach.id
                             WHERE  eval.sequence = :seq
                             AND  eval.classRoom = :room
                             AND  std.id = :stdId
                             ORDER BY  module, crs.wording"
            )->setParameter('seq', $sequence->getId())
            ->setParameter('room', $sub->getClassRoom()->getId())
            ->setParameter('stdId', $sub->getStudent()->getId());
        dump($query->getSql());
        //  ->setFirstResult(150) ->setMaxResults(550)

        return $query->getResult();
    }
}
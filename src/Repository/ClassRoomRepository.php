<?php

namespace App\Repository;
use App\Entity\SchoolYear;
use App\Entity\ClassRoom;
use App\Entity\Subscription;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClassRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassRoom[]    findAll()
 * @method ClassRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassRoom::class);
    }
    // Nombre de salles de classe ayant au moin un eleve inscrit
    public function countEnabledClassRoom(SchoolYear $year){
        $queryBuilder = $this->createQueryBuilder('cl')
        ->select('COUNT(cl.id) AS effectif, cl.id')
        ->join('cl.subscriptions', 's')
        ->andWhere('s.schoolYear = :year')
        ->groupBy('cl.id')
        ->setParameter('year', $year->getId());
         
         $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    public function countSuccessfulStudentsForClass(ClassRoom $classRoom)
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->select('COUNT(s.id) AS successfulCount')
            ->join('cl.subscriptions', 's')
            ->where('s.financialHolder = 0') // Inscrits
            ->andWhere('s.officialExam <> 0') // Réussis
            ->andWhere('cl = :classRoom')
            ->setParameter('classRoom', $classRoom);

        $query = $queryBuilder->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // Méthode pour obtenir le nombre d'élèves n'ayant pas réussi pour une classe donnée
    public function countUnsuccessfulStudentsForClass(ClassRoom $classRoom)
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->select('COUNT(s.id) AS unsuccessfulCount')
            ->join('cl.subscriptions', 's')
            ->where('s.financialHolder = 0') // Inscrits
            ->andWhere('s.officialExam = 0') // Non réussis
            ->andWhere('cl = :classRoom')
            ->setParameter('classRoom', $classRoom);

        $query = $queryBuilder->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    // Méthode pour obtenir les statistiques de mentions pour une classe donnée
    public function getMentionStatisticsForClass(ClassRoom $classRoom)
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->select('s.officialExam AS mention, COUNT(s.id) AS mentionCount')
            ->join('cl.subscriptions', 's')
            ->where('cl = :classRoom')
            ->andWhere('s.officialExam <> 0') // Uniquement les élèves ayant passé l'examen
            ->groupBy('s.officialExam')
            ->setParameter('classRoom', $classRoom);

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        $mentionStatistics = [];
        foreach ($results as $result) {
            $mention = $result['mention'];
            $mentionCount = $result['mentionCount'];
            $verbalMention = $this->getVerbalOfficialExamResult($mention); // Utilisez la méthode pour obtenir la mention verbale
            $mentionStatistics[$verbalMention] = $mentionCount;
        }

        return $mentionStatistics;
    }

    public function getClassRoomSuccessStatisticsByLevel($level)
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->select('cl.name AS className, COUNT(s.id) AS successfulCount')
            ->join('cl.subscriptions', 's')
            ->where('s.financialHolder = 0') // Inscrits
            ->andWhere('s.officialExam <> 0') // Réussis
            ->andWhere('cl.level = :level')
            ->groupBy('cl.name')
            ->setParameter('level', $level);

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        $successStatistics = [];
        foreach ($results as $result) {
            $className = $result['className'];
            $successfulCount = $result['successfulCount'];
            $successStatistics[$className] = $successfulCount;
        }

        return $successStatistics;
    }




    // /**
    //  * @return ClassRoom[] Returns an array of ClassRoom objects
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
    public function findOneBySomeField($value): ?ClassRoom
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

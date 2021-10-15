<?php

namespace App\Repository;

use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    // /**
    //  * @return Evaluation[] Returns an array of Evaluation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evaluation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAll() {
        $qb = $this->createQueryBuilder('e')
                  ->orderBy('e.sequence', 'DESC')
                 ->orderBy('e.classRoom', 'DESC')
                 ->orderBy('e.id', 'DESC');
          return $qb->getQuery()->getResult();
    }

    // Liste des évaluation d'une salle de classe à une séquence
    public function findSequantialExamsOfRoom(int $room, int $seq)
    {
        $qb = $this->createQueryBuilder('e')
                ->leftJoin('e.classRoom', 'r')
                ->leftJoin('e.sequence', 's')
                ->leftJoin('e.course', 'c')
                ->where('r.id=:room')
                ->andWhere('s.id=:seq')
                ->orderBy('c.domain')
                ->orderBy('c.wording')
                ->setParameter('seq', $seq)
                ->setParameter('room', $room);
        return $qb->getQuery() ->getResult();
    }

    // Liste des évaluation de l'année scolaire spécifiée
    public function findAnnualEvaluations( int $sc)
    {
        $qb = $this->createQueryBuilder('e')
                ->leftJoin('e.sequence', 's')
                ->leftJoin('s.quater', 'q')
                ->leftJoin('q.schoolYear', 'sc')
                ->andWhere('sc.id=:sc')
                ->setParameter('sc', $sc)
                ->orderBy('e.sequence', 'DESC')
                ->orderBy('e.id', 'DESC');
        return $qb->getQuery() ->getResult();
    }
}

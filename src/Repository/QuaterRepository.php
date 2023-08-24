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



    public function findQuaterThisYear(SchoolYear $year)
    {
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


    /**
     * Récupère toutes les années scolaires activées, sauf celle spécifiée.
     *
     * @param Quater $quat trimestre spécifique à exclure.
     * @return Quater[] Un tableau de trimestres activés, excluant la spécifique.
     */
    public function findAllExcept(Quater $quat): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q != :qt') // Exclut le trimestre  spécifique
            ->setParameter('qt', $quat)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return number of Quater
     */
    public function countActivatedExcept(Quater $quat)
    {
        $query = $this->createQueryBuilder('q')
            ->select('COUNT(q) as count')
            ->where('q.activated=:val')
            ->andWhere('q != :quat') // Exclut l'année scolaire spécifique
            ->setParameter('quat', $quat)
            ->setParameter('val', true);
        return $query->getQuery()->getResult();
    }
}

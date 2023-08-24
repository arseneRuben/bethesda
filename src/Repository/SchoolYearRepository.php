<?php

namespace App\Repository;

use App\Entity\Traits\Period;
use App\Entity\SchoolYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Le repository pour l'entité SchoolYear.
 */
class SchoolYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolYear::class);
    }

    /**
     * Récupère toutes les années scolaires activées, sauf celle spécifiée.
     *
     * @param SchoolYear $schoolYear L'année scolaire spécifique à exclure.
     * @return SchoolYear[] Un tableau d'années scolaires activées, excluant la spécifique.
     */
    public function findAllActivatedExcept(SchoolYear $schoolYear): array
    {
        return $this->createQueryBuilder('sy')
            ->andWhere('sy != :schoolYear') // Exclut l'année scolaire spécifique
            ->setParameter('schoolYear', $schoolYear)
            ->andWhere('sy.activated = true') // Sélectionne les années scolaires activées (activated = true)
            ->getQuery()
            ->getResult();
    }

      /**
     * Récupère toutes les années scolaires activées, sauf celle spécifiée.
     *
     * @param SchoolYear $schoolYear L'année scolaire spécifique à exclure.
     * @return SchoolYear[] Un tableau d'années scolaires activées, excluant la spécifique.
     */
    public function findAllExcept(SchoolYear $schoolYear): array
    {
        return $this->createQueryBuilder('sy')
            ->andWhere('sy != :schoolYear') // Exclut l'année scolaire spécifique
            ->setParameter('schoolYear', $schoolYear)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return number of subscriptions per year in a ClassRoom
     */
    public function countActivatedExcept(SchoolYear $year)
    {
        $query = $this->createQueryBuilder('s')
            ->select('COUNT(s) as count')
            ->where('s.activated=:val')
            ->andWhere('s != :year') // Exclut l'année scolaire spécifique
            ->setParameter('year', $year)
            ->setParameter('val', true);
        return $query->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\TuitionPlan; // Remplacez SettingsPayments par TuitionPlan
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TuitionPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method TuitionPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method TuitionPlan[]    findAll()
 * @method TuitionPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TuitionPlanRepository extends ServiceEntityRepository // 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TuitionPlan::class);
    }

    public function getTuitionPlanAndAmountsForClassRoom(int $classRoomId)
    {
        return $this->createQueryBuilder('tp')
            ->select('tp.name, tp.amount')
            ->innerJoin('tp.classRoom', 'c')
            ->where('c.id = :classRoomId')
            ->setParameter('classRoomId', $classRoomId)
            ->orderBy('tp.createdAt', 'DESC') // Tri par ordre décroissant de date de création
            ->getQuery()
            ->getResult();
    }

    /**
     * Crée un tableau de montants de tranches où chaque tranche est égale à la somme de toutes les tranches précédentes.
     *
     * @param int $classRoomId L'ID de la classe.
     * @return array|null Un tableau associatif des montants de tranches, ou null si aucun résultat.
     */
    public function getAccumulatedTuitionPlanAmountsForClassRoom(int $classRoomId)
    {
        $tranches = $this->createQueryBuilder('tp')
            ->select('tp.tranche, tp.amount')
            ->innerJoin('tp.classRoom', 'c')
            ->where('c.id = :classRoomId')
            ->setParameter('classRoomId', $classRoomId)
            ->orderBy('tp.createdAt', 'ASC') // Tri par ordre croissant de date de création
            ->getQuery()
            ->getResult();

        $accumulatedAmounts = [];
        $totalAmount = 0;

        foreach ($tranches as $tranche) {
            $totalAmount += $tranche['amount'];
            $accumulatedAmounts[$tranche['tranche']] = $totalAmount;
        }

        return $accumulatedAmounts;
    }


    // Ajoutez ici vos méthodes personnalisées si nécessaire
}

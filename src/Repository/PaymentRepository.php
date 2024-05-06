<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Quater;
use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Service\SchoolYearService;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    private SchoolYearService      $schoolYearService;

    public function __construct(ManagerRegistry $registry, SchoolYearService $schoolYearService)
    {
        parent::__construct($registry, Payment::class);
        $this->schoolYearService = $schoolYearService;

    }

    // /**
    //  * @return Payment[] Returns an array of Payment objects
    //  */
    
    public function findPayments(SchoolYear $year=null, int $room=null, int $qt=null, int $std=null, \DateTime $begin=null, \DateTime $end=null)
    {
        /*$request = "SELECT pmt FROM App\Entity\Payment  pmt ";

        if($std != null){
            $request .= " JOIN App\Entity\Student std ON  pmt.student  =   :std ";
        }
        if($room != null){
            $request .= " JOIN App\Entity\Subscription sub ON  sub.student  =  std.id AND  sub.classRoom = :room ";
        }
        if($year != null){
            $request .= " AND sub.schoolYear = :year ";
        }
        if($begin != null){
            $request .= " AND pmt.startDate >= :begin ";
        }
        if($end != null){
            $request .= " AND  pmt.endDate <= :end ";
        }
        
        $query=$this->getEntityManager()
        ->createQuery(
            $request
        );
        if($std != null){
            $query->setParameter('std', $std->getId());
        }
        if($room != null){
            $query->setParameter('room', $room->getId());
        }
        if($year != null){
            $query->setParameter('year', $year->getId());
        }
        if($end != null){
            $query->setParameter('end', $end);
        }
        if($begin != null){
            $query->setParameter('begin',$begin);
        }
        return $query->getResult(); */
        $qb = $this->createQueryBuilder('p');
        if($std != 0){
            $qb->leftJoin('p.student', 'std')
            ->andWhere('std = :std_id')
            ->setParameter('std_id', $std);
        }
        if($qt != 0){
            $qb->join('p.schoolYear', 's')
            ->andWhere('s = :s_id')
            ->join('s.quaters', 'qt')
            ->andWhere('qt = :qt_id')
            ->setParameter('s_id', $this->schoolYearService->sessionYearById()->getId())
            ->setParameter('qt_id', $qt);

        }
        if($room != 0){
            $qb->join('p.schoolYear', 'sc')
            ->andWhere('sc = :sc_id')
            ->setParameter('sc_id', $this->schoolYearService->sessionYearById()->getId())
            ->join('sc.subscriptions', 'sub')
            ->join('sub.classRoom', 'room')
            ->andWhere('room = :room_id')
            ->setParameter('room_id', $room);

        }
        if($begin != null){
            $qb->andWhere('p.updatedAt >= :begin')
               ->setParameter('begin',$begin);
        }
        if($end != null){
            $qb->andWhere('p.updatedAt <= :end')
               ->setParameter('end', $end);
        }
        return $qb->getQuery()->execute();;
    }
    

   
}

<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\UserRepository;


class StatistiquesServices
{
    private SubscriptionRepository $subRepo;
    private SchoolYearRepository $scRepo;
    private ClassRoomRepository $roomRepo;
    private UserRepository $userRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo, SubscriptionRepository $subRepo)
    {

        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->scRepo = $scRepo;
        $this->roomRepo = $rmRepo;
        $this->subRepo = $subRepo;
    }

    public function teachers()
    {

        $year = $this->scRepo->findOneBy(array("activated" => true));
        $qb = $this->em->createQueryBuilder();

        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->leftJoin(
                'App\Entity\Attribution',
                'a',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $qb->expr()->eq('a.teacher_id', 'u.id')
            )
            ->groupBy('u.id')
            ->where($qb->expr()->eq('a.year_id', $year->getId()))
            ->andWhere($qb->expr()->gte('count(a.id)', 1));

        $userIds =  $qb->getQuery()->getResult();

        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where($qb->expr()->in('u.id', ':userIds'))
            ->setParameter('userIds', $userIds);
        $users = $qb->getQuery()->getResult();
        return count($users);
    }

    public function students()
    {
    }

    public function rooms()
    {
    }
}

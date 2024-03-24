<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\UserRepository;


class StatistiquesService
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
        $users = $this->userRepo->findAllOfCurrentYear($year);
        return count($users);
    }

    public function students()
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $qb = $this->em->createQueryBuilder();
        $students = $this->subRepo->findBy(array("schoolYear" => $year));
        return count($students);
    }

    public function rooms()
    {
    }
}

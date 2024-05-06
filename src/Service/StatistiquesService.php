<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\UserRepository;
use App\Service\SchoolYearService;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class StatistiquesService
{
    private SubscriptionRepository $subRepo;
    private SchoolYearRepository $scRepo;
    private ClassRoomRepository $roomRepo;
    private UserRepository $userRepo;
    private SessionInterface $session;
    private SchoolYearService $schoolYearService;
    private EntityManagerInterface $em;

    public function __construct( SchoolYearService $schoolYearService, EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo, SubscriptionRepository $subRepo, SessionInterface $session)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->scRepo = $scRepo;
        $this->roomRepo = $rmRepo;
        $this->subRepo = $subRepo;
        $this->session = $session;
        $this->schoolYearService = $schoolYearService;
    }

    public function teachers()
    {
        
        
        $users = $this->userRepo->findAllOfCurrentYear($this->schoolYearService->sessionYearById());
        return count($users);
    }

    public function students()
    {
        $students = $this->subRepo->findBy(array("schoolYear" => $this->schoolYearService->sessionYearById()));
        return count($students);
    }

    public function rooms()
    {
        $roomsEnabled = $this->roomRepo->countEnabledClassRoom($this->schoolYearService->sessionYearById());
        return count($roomsEnabled);

    }
}

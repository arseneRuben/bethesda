<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class StatistiquesService
{
    private SubscriptionRepository $subRepo;
    private SchoolYearRepository $scRepo;
    private ClassRoomRepository $roomRepo;
    private UserRepository $userRepo;
    private SessionInterface $session;
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo, SubscriptionRepository $subRepo, SessionInterface $session)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->scRepo = $scRepo;
        $this->roomRepo = $rmRepo;
        $this->subRepo = $subRepo;
        $this->session = $session;
    }

    public function teachers()
    {
        $year = ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->session->get('session_school_year') : $this->scRepo->findOneBy(array("activated" => true));
        $users = $this->userRepo->findAllOfCurrentYear($year);
        return count($users);
    }

    public function students()
    {
        $year = ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->session->get('session_school_year') : $this->scRepo->findOneBy(array("activated" => true));
        $students = $this->subRepo->findBy(array("schoolYear" => $year));
        return count($students);
    }

    public function rooms()
    {
        $year = ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->session->get('session_school_year') : $this->scRepo->findOneBy(array("activated" => true));
        $roomsEnabled = $this->roomRepo->countEnabledClassRoom($year);
        return count($roomsEnabled);

    }
}

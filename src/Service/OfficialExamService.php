<?php

namespace App\Service;

use App\Entity\SchoolYear;
use App\Entity\ClassRoom;
use App\Repository\SubscriptionRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;

class OfficialExamService
{
    private SubscriptionRepository $subRepo;
    private SchoolYearRepository $scRepo;
    private ClassRoomRepository $roomRepo;

    public function __construct(SubscriptionRepository $subRepo, SchoolYearRepository $scRepo, ClassRoomRepository $roomRepo)
    {
        $this->subRepo = $subRepo;
        $this->scRepo = $scRepo;
        $this->roomRepo = $roomRepo;
    }

    public function successRate(int $roomId)
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $room = $this->roomRepo->findOneBy(array("id" => $roomId));
        $candidates = $this->subRepo->countCandidates($year, $room);
        $success =  $this->subRepo->countSuccessfullCandidates($year, $room);

        return $success[0]["count"] / $candidates[0]["count"];
    }

    public function subscriptions(int $roomId)
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $room = $this->roomRepo->findOneBy(array("id" => $roomId));
        $candidates = $this->subRepo->findBy(array("schoolYear" => $year, "classRoom" => $room));

        return  $candidates;
    }
}

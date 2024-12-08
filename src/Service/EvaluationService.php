<?php

namespace App\Service;

use App\Entity\Evaluation;
use App\Entity\User;

use App\Repository\EvaluationRepository;
use App\Repository\AttributionRepository;


class EvaluationService
{
    private EvaluationRepository $evaluationRepo;
    private  AttributionRepository $attrRepo;
    private SchoolYearService $schoolYearService;


    public function __construct(  SchoolYearService $schoolYearService,AttributionRepository $attrRepo,EvaluationRepository $evaluationRepo)
    {
        $this->evaluationRepo = $evaluationRepo;
        $this->attrRepo = $attrRepo;
        $this->schoolYearService = $schoolYearService;

    }

    public function getTeacher(Evaluation $entity) : User
    {
        $year = $this->schoolYearService->sessionYearById();
        $attribution  = $this->attrRepo->findOneBy(array("schoolYear" => $year, "course" => $this->getCourse()));
        return $attribution->getTeacher();
    }
}

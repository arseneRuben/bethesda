<?php

namespace App\Service;
use App\Repository\SchoolYearRepository;

class SchoolYearService
{
    private SchoolYearRepository $scRepo;

    public function __construct( SchoolYearRepository $scRepo)
    {
        $this->scRepo = $scRepo;
    }

    public function years()
    {
       
        return $this->scRepo->findAll(array('id' => 'ASC'));
    }

    
    public function updateEnabledSchoolYear()
    {
        return $this->scRepo->findAll(array('id' => 'ASC'));
    }




}
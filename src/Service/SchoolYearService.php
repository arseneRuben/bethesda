<?php

namespace App\Service;
use App\Repository\SchoolYearRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SchoolYearService
{
    private SchoolYearRepository $scRepo;
    private SessionInterface $session;

    public function __construct( SchoolYearRepository $scRepo, SessionInterface $session)
    {
        $this->scRepo = $scRepo;
        $this->session = $session;
    }

    public function years()
    {
       
        return $this->scRepo->findAll(array('id' => 'ASC'));
    }
    public function sessionYearByCode()
    {
        return ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->scRepo->findOneBy(array("code" => $this->session->get('session_school_year')))  : $this->scRepo->findOneBy(array("activated" => true));
    }
    public function sessionYearById()
    {
        return ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->scRepo->findOneBy(array("id" => $this->session->get('session_school_year')))  : $this->scRepo->findOneBy(array("activated" => true));
    }

    public function enabledYear($id)
    {
        return $this->scRepo->findOneBy(array('id' => $id));
    }
    
    
    public function updateEnabledSchoolYear()
    {
        return $this->scRepo->findAll(array('id' => 'ASC'));
    }




}
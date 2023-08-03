<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Entity\Subscription;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SchoolYearRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class StudentController extends ServiceEntityRepository
{
    public function findAll(){
        
    }
}
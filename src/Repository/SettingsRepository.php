<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SchoolYearRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsRepository 
{
    public function __construct()
    {
    }

    public function save($logoUrl, $schoolName)
    {
        
    }


}

<?php

namespace App\Entity;
use App\Entity\User;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Repository\MainTeacherRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MainTeacherRepository::class)
 * @UniqueEntity(fields={"classRoom", "schoolYear"}, message= "There is already a MainTeacher in this class at this year")
 */
class MainTeacher
{
    use TimeStampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mainTeachers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $teacher;
    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classRoom;
    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $schoolYear;
    public function __construct()
    {
        $this->updateTimestamp();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTeacher(User $teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

   
    public function getTeacher()
    {
        return $this->teacher;
    }

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }


    public function setClassRoom(?ClassRoom $classRoom): self
    {
        $this->classRoom = $classRoom;

        return $this;
    }

    public function setSchoolYear(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }
}

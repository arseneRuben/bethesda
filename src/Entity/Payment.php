<?php

namespace App\Entity;

use App\Entity\Student;
use App\Entity\SchoolYear;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;
use App\Entity\Traits\Amount;
/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{

    use TimeStampable;
    use Amount;
    public const NUM_ITEMS_PER_PAGE = 20;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;




    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class)
     * @ORM\JoinColumn(name="school_year_id", referencedColumnName="id", nullable=true)
     */
    private $schoolYear;


    /**
     * @ORM\ManyToOne(targetEntity=Student::class)
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true)
     */
    private $student;


    public function __construct()
    {
  
        $this->createdAt= new \DateTime();
        $this->updatedAt= new \DateTime();
       
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set schoolYear
     *
     * @param SchoolYear $schoolYear
     *
     * @return Payment
     */
    public function setSchoolYear(SchoolYear $schoolYear = null)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

     /**
     *  Get schoolYear
     * 
     * @return SchoolYear
     */
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }



    /**
     * Set student
     *
     * @param Student $student
     *
     * @return Payment
     */
    public function setStudent(Student $student = null)
    {
        $this->student = $student;

        return $this;
    }



    /**
     * Get student
     *
     * @return Student
     */
    public function getStudent()
    {
        return $this->student;
    }


}




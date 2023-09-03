<?php

namespace App\Entity;

use App\Entity\Student;
use App\Entity\SchoolYear;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{

    use TimeStampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;


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
     * Set instant
     *
     * @param \DateTime $instant
     *
     * @return Payment
     * /
    public function setInstant($instant)
    {
        if (is_string($instant)) {
            $this->instant = \DateTime($instant);
        } else {
            $this->instant = $instant;
        }

        return $this;
    }



    /**
     * Get instant
     *
     * @return \DateTime
     * /
    public function getInstant()
    {
        return $this->instant;
    }

   

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }



    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
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




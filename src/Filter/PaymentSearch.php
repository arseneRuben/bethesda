<?php

namespace App\Filter;
use App\Entity\Student;
use App\Entity\Quater;
use App\Entity\ClassRoom;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Period;

/**
 * @ORM\Entity()
 */
class PaymentSearch
{
    use Period;
    private ClassRoom $room;
    private Quater $quater;
    private Student $student;


    public function __construct()
    {
    }

    public function getRoom(): ?ClassRoom
    {
        return $this->room;
    }

    public function setRoom(?ClassRoom $room): self
    {
        $this->classRoom = $room;

        return $this;
    }

    public function getQuater(): ?Quater
    {
        return $this->quater;
    }

    public function setQuater(?Quater $quater): self
    {
        $this->quater = $quater;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }


}

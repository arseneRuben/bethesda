<?php

namespace App\Filter;

use App\Entity\Course;
use App\Entity\Sequence;
use App\Entity\ClassRoom;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PropertySearch
{
    private ClassRoom $room;
    private Course $course;
    private Sequence $sequence;


    public function __construct()
    {
    }

    /**
     * @return Sequence|null
     */
    public function getSequence(): ?Sequence
    {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }
}

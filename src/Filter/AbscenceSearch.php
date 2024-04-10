<?php

namespace App\Filter;

use App\Entity\ClassRoom;
use App\Entity\Sequence;
use App\Entity\Quater;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity()
 */
class AbscenceSearch
{
    private ClassRoom $room;

    private Sequence $sequence;

    private Quater $quater;



    public function __construct()
    {
    }

    /**
     * @return Quater|null
     */
    public function getQuater(): ?Quater
    {
        return $this->quater;
    }

    public function setQuater(?Quater $quater): self
    {
        $this->quater = $quater;

        return $this;
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

    public function setRoom(?ClassRoom $classRoom): self
    {
        $this->room = $classRoom;

        return $this;
    }
}

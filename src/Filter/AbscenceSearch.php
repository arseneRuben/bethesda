<?php

namespace App\Filter;

use App\Entity\ClassRoom;
use App\Entity\Sequence;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity()
 */
class AbscenceSearch
{
    private ClassRoom $room;

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

    public function setRoom(?ClassRoom $classRoom): self
    {
        $this->room = $classRoom;

        return $this;
    }
}

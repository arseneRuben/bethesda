<?php

namespace App\Filter;


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


}

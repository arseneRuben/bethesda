<?php

namespace App\Entity\Traits;
trait Amount  {

    
    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;


      /**
     * Set amount
     *
     * @param integer $amount
     *
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
}
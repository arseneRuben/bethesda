<?php

namespace App\Entity;

use App\Repository\InstallmentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Amount;
use App\Entity\Traits\TimeStampable;

/**
 * Tranche de scolarite
 *
 * @ORM\Table(name="installment")
 * @ORM\Entity(repositoryClass=InstallmentRepository::class)
 */
class Installment
{
    use Amount;
    use TimeStampable;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentPlan::class)
     * @ORM\JoinColumn(name="payment_plan_id", referencedColumnName="id", nullable=true)
     */
    private $paymentPlan;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentPlan(): ?PaymentPlan
    {
        return $this->paymentPlan;
    }

    public function setPaymentPlan(?PaymentPlan $paymentPlan): static
    {
        $this->paymentPlan = $paymentPlan;

        return $this;
    }

    
}

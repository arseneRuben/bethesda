<?php

namespace App\Entity;

use App\Repository\InstallmentRepository;
use Doctrine\DBAL\Types\Types;
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

      /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;
      /**
     * @var \Datetime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=false)
     */
    protected $deadline;
   

    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $order): static
    {
        $this->rank = $order;

        return $this;
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    
}

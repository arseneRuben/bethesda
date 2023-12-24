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
     * @ORM\Column(name="order", type="integer")
     */
    private $order;
      /**
     * @var \Date
     *
     * @ORM\Column(name="deadline", type="date", nullable=true)
     */
    protected $deadline;
   

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

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterfaces
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    
}

<?php

namespace App\Entity;

use App\Entity\SchoolYear;
use App\Entity\ClassRoom;
use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentPlan
 *
 * @ORM\Table(name="payment_plan")
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class PaymentPlan
{
  

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\OneToOne(targetEntity=SchoolYear::class)
     * @ORM\JoinColumn(name="school_year_id", referencedColumnName="id", nullable=true)
     */
    private $schoolYear;


    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class) 
     * @ORM\JoinColumn(name="classRoom_id", referencedColumnName="id", nullable=true)
     */
    private $classRoom;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="paymentPlan")
     */
    private $payments;
     /**
     * @ORM\OneToMany(targetEntity=Installment::class, mappedBy="paymentPlan")
     */
    private $installments;
   

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->installments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchoolYear(): ?SchoolYear
    {
        return $this->schoolYear;
    }

    public function setSchoolYear(?SchoolYear $schoolYear): static
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }

    public function setClassRoom(?ClassRoom $classRoom): static
    {
        $this->classRoom = $classRoom;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setPaymentPlan($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getPaymentPlan() === $this) {
                $payment->setPaymentPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Installment>
     */
    public function getInstallments(): Collection
    {
        return $this->installments;
    }

    public function addInstallment(Installment $installment): static
    {
        if (!$this->installments->contains($installment)) {
            $this->installments->add($installment);      
        }

        return $this;
    }

    public function removeInstallment(Installment $installment): static
    {
        $this->installments->removeElement($installment);
        return $this;
    }
}

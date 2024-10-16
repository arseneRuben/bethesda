<?php

namespace App\Entity;
use App\Entity\ClassRoom;
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
     * @ORM\ManyToOne(targetEntity=PaymentPlan::class,inversedBy="installments")
     * @ORM\JoinColumn(name="payment_plan_id", referencedColumnName="id", nullable=true)
     */
    private $paymentPlan;
     /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class) 
     * @ORM\JoinColumn(name="classRoom_id", referencedColumnName="id", nullable=true)
     */
    private $classRoom;
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
     * @ORM\Column(name="ranking", type="integer")
     */
    private $ranking;
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

   

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(int $order): static
    {
        $this->ranking = $order;
        if($this->getPaymentPlan()->getWeight() < $order){
            $this->getPaymentPlan()->setWeight($order) ;
        }
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

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }

    public function setClassRoom(?ClassRoom $classRoom): static
    {
        $this->classRoom = $classRoom;

        return $this;
    }

    
}

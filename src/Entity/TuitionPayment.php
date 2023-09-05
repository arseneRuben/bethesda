<?php

namespace App\Entity;

use App\Entity\Student;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TuitionPaymentRepository")
 */
class TuitionPayment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $paymentDate;

    // Les getters et setters de chaque propriété

    public function getId()
    {
        return $this->id;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }
}

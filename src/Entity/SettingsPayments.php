<?php

namespace App\Entity;

use App\Repository\SettingsPaymentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=SettingsPaymentsRepository::class)
 */
class SettingsPayments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="settingsPayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="date")
     */
    private $deadLine;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class, inversedBy="settingsPayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $schoolYear;

     /** @ORM\Column(name="reason", nullable=true, unique=false, length=12) 
     * @Assert\Choice(
     * choices = { "INSCRIPTION", "TRANCHE1" , "TRANCHE2", "TRANCHE3"},
     * message = "précisez le type de scolarité")
     */
    private $reason;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->deadLine;
    }

    public function setDeadLine(\DateTimeInterface $deadLine): self
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSchoolYear(): ?SchoolYear
    {
        return $this->schoolYear;
    }

    public function setSchoolYear(?SchoolYear $schoolYear): self
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }


    public function __toString() {
        $name = ( is_null($this->getReason())) ? "" : $this->getReason();
        $level = ( is_null($this->getLevel())) ? "" : $this->getLevel();
       
        $year= ( is_null($this->getSchoolYear())) ? "" : $this->getSchoolYear();
        return (string) ($name.'_'.$level.'_'.$year );
    }
}

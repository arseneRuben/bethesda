<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TuitionPlanRepository")
 */
class TuitionPlan
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $numberOfInstallments;

    // Les getters et setters pour chaque propriété

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * setName
     *
     * @param string $name
     *
     * @return TuitionPlan
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setAmount
     *
     * @param mixed $amount
     *
     * @return TuitionPlan
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * getAmount
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * setNumberOfInstallments
     *
     * @param int $numberOfInstallments
     *
     * @return TuitionPlan
     */
    public function setNumberOfInstallments(int $numberOfInstallments): self
    {
        $this->numberOfInstallments = $numberOfInstallments;
        return $this;
    }

    /**
     * getNumberOfInstallments
     *
     * @return int|null
     */
    public function getNumberOfInstallments(): ?int
    {
        return $this->numberOfInstallments;
    }
}

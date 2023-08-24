<?php

namespace App\Entity;

use App\Repository\SequenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Period;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=SequenceRepository::class)
 */
class Sequence implements JsonSerializable
{
    use Period;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Quater::class, inversedBy="sequences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quater;

    /**
     * @ORM\Column(type="datetime")
     */
    private $validationDate;

    /**
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="sequence", orphanRemoval=true)
     */
    private $evaluations;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        return [

            'wording' => strtolower($this->wording),
        ];
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuater(): ?Quater
    {
        return $this->quater;
    }

    public function setQuater(?Quater $quater): self
    {
        $this->quater = $quater;

        return $this;
    }


    public function getValidationDate(): ?\DateTimeInterface
    {
        return $this->validationDate;
    }

    public function setValidationDate(\DateTimeInterface $validationDate): self
    {
        $this->validationDate = $validationDate;

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setSequence($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getSequence() === $this) {
                $evaluation->setSequence(null);
            }
        }

        return $this;
    }


    public function unable()
    {
        $this->setActivated(true);
    }

    public function disable()
    {
        $this->setActivated(false);
    }
}

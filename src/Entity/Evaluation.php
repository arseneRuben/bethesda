<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 */
class Evaluation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sequence::class, inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    /**
     * @ORM\Column(type="float")
     */
    private $moyenne;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $competence;

    /**
     * @ORM\Column(type="integer")
     */
    private $abscent;

    /**
     * @ORM\Column(type="integer")
     */
    private $successH;

    /**
     * @ORM\Column(type="integer")
     */
    private $successF;

    /**
     * @ORM\Column(type="integer")
     */
    private $failluresH;

    /**
     * @ORM\Column(type="integer")
     */
    private $failluresF;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $classRoom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSequence(): ?Sequence
    {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getMoyenne(): ?float
    {
        return $this->moyenne;
    }

    public function setMoyenne(float $moyenne): self
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    public function getCompetence(): ?string
    {
        return $this->competence;
    }

    public function setCompetence(?string $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getAbscent(): ?int
    {
        return $this->abscent;
    }

    public function setAbscent(int $abscent): self
    {
        $this->abscent = $abscent;

        return $this;
    }

    public function getSuccessH(): ?int
    {
        return $this->successH;
    }

    public function setSuccessH(int $successH): self
    {
        $this->successH = $successH;

        return $this;
    }

    public function getSuccessF(): ?int
    {
        return $this->successF;
    }

    public function setSuccessF(int $successF): self
    {
        $this->successF = $successF;

        return $this;
    }

    public function getFailluresH(): ?int
    {
        return $this->failluresH;
    }

    public function setFailluresH(int $failluresH): self
    {
        $this->failluresH = $failluresH;

        return $this;
    }

    public function getFailluresF(): ?int
    {
        return $this->failluresF;
    }

    public function setFailluresF(int $failluresF): self
    {
        $this->failluresF = $failluresF;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }

    public function setClassRoom(?ClassRoom $classRoom): self
    {
        $this->classRoom = $classRoom;

        return $this;
    }
}

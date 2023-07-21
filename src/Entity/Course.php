<?php

namespace App\Entity;

use App\Entity\Evaluation;
use App\Entity\Attribution;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CourseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Domain::class, inversedBy="courses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $domain;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="courses")
     */
    private $module;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\Column(type="integer")
     */
    private $coefficient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $attributed = false;

    /**
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="course")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity=Attribution::class, mappedBy="course",cascade={"persist"})    
     * @ORM\JoinColumn(nullable=true)
     *    
     * */
    private $attributions;

    public function currentTeacher()
    {

        $teacher = null;
        if (!$this->attributions->isEmpty()) {
            $teacher = $this->attributions->last()->getTeacher();
        }
        return $teacher;
    }

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->attributions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        $domain = (is_null($this->getDomain())) ? "" : $this->getDomain();
        $wording = (is_null($this->getWording())) ? "" : $this->getWording();
        $code = (is_null($this->getCode())) ? "" : $this->getCode();
        return (string) ($domain . "/" . $code . "_" . $wording);
    }


    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }



    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAttributed(): ?bool
    {
        return $this->attributed;
    }

    public function setAttributed(bool $attributed): self
    {
        $this->attributed = $attributed;
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
            $evaluation->setCourse($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getCourse() === $this) {
                $evaluation->setCourse(null);
            }
        }

        return $this;
    }



    public function addAttribution(Attribution $attribution)
    {
        $this->attributions[] = $attribution;

        return $this;
    }


    public function removeAttribution(Attribution $attribution)
    {
        $this->attributions->removeElement($attribution);
    }

    /**
     * Get attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributions()
    {
        return $this->attributions;
    }
}
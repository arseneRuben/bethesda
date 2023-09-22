<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampable;
use App\Repository\AbscenceSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=AbscenceSheetRepository::class)
 */ class AbscenceSheet
{
    use TimeStampable;

    public const NUM_ITEMS_PER_PAGE = 20;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sequence::class, inversedBy="abscenceSheets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sequence;


    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="abscenceSheets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classRoom;



    /**
     * @ORM\OneToMany(targetEntity=Abscence::class, mappedBy="abscenceSheet", orphanRemoval=true)
     */
    private $abscences;

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     */
    private $endDate;


    public function __construct()
    {
        $this->abscences = new ArrayCollection();
        $this->updateTimestamp();
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function addAbscence(Abscence $abscence): self
    {
        if (!$this->abscences->contains($abscence)) {
            $this->abscences[] = $abscence;
            $abscence->setAbscenceSheet($this);
        }

        return $this;
    }
    public function removeAbscence(Abscence $abscence): self
    {
        if ($this->abscences->removeElement($abscence)) {
            // set the owning side to null (unless already changed)
            if ($abscence->getAbscenceSheet() === $this) {
                $abscence->setAbscenceSheet(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|Abscence[]
     */
    public function getAbscences(): Collection
    {
        return $this->abscences;
    }

    public function getTotalAbscence(): int
    {
        return count($this->abscences);
    }

    public function getTotalAbscenceHourByStudent(Student $student): int
    {
        $total = 0;
        foreach ($this->abscences as $abscence) {
            if ($abscence->getStudent() === $student) {
                $total += $abscence->getHour();
            }
        }
        return $total;
    }

    public function getTotalAbscenceHour(): int
    {
        $total = 0;
        foreach ($this->abscences as $abscence) {
            $total += $abscence->getHour();
        }
        return $total;
    }




    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSequence(): ?Sequence
    {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): static
    {
        $this->sequence = $sequence;

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

<?php

namespace App\Entity;

use App\Entity\Traits\Period;
use App\Repository\QuaterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuaterRepository::class)
 */
class Quater
{
    use Period;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class, inversedBy="quaters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $schoolYear;

    /**
     * @ORM\OneToMany(targetEntity=Sequence::class, mappedBy="quater", orphanRemoval=true)
     */
    private $sequences;

    public function __construct()
    {
        $this->sequences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Sequence[]
     */
    public function getSequences(): Collection
    {
        return $this->sequences;
    }

    public function addSequence(Sequence $sequence): self
    {
        if (!$this->sequences->contains($sequence)) {
            $this->sequences[] = $sequence;
            $sequence->setQuater($this);
        }

        return $this;
    }

    public function removeSequence(Sequence $sequence): self
    {
        if ($this->sequences->removeElement($sequence)) {
            // set the owning side to null (unless already changed)
            if ($sequence->getQuater() === $this) {
                $sequence->setQuater(null);
            }
        }

        return $this;
    }

    public function unable()
    {
        $this->setActivated(true);
        if (count($this->getSequences()) > 0)
            $this->getSequences()[0]->unable();
    }

    public function disable()
    {
        $this->setActivated(false);
        foreach ($this->getSequences() as $sequence) {
            $sequence->disable();
        }
    }
}

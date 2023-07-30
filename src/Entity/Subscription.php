<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    use TimeStampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classRoom;

    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $schoolYear;

    /*
        0  : Echec
        1p : Success  Passable
        1a : Success  Assez-bien
        1b : Success  Bien
        1t : Success  Tres-Bien
        1e : Success  Excellent
        A : 5 points
        B : 4 points
        C : 3 points
        D : 2 points
        E : 1 point

    */
    /**
     * @var string
     *
     * @ORM\Column(name="officialExamResult", type="string", length=10 , options={"default" = "1p"})
     */
    private $officialExamResult;


    /**
     * @ORM\Column(type="boolean")
     */
    private $financeHolder;
    public function __construct()
    {

        $this->updateTimestamp();
        $this->financeHolder = false;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }


    public function setClassRoom(?ClassRoom $classRoom): self
    {
        $this->classRoom = $classRoom;

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

    public function getFinanceHolder(): ?bool
    {
        return $this->financeHolder;
    }

    public function setFinanceHolder(bool $financeHolder): self
    {
        $this->financeHolder = $financeHolder;

        return $this;
    }

    public function getOfficialExamResult(): ?string
    {
        return $this->officialExamResult;
    }

    public function getVerbalOfficialExamResult()
    {
        $result = "PASSABLE";
        switch ($this->officialExamResult) {
            case "0":
                $result = "ECHEC";
                break;
            case "1p":
                $result = "PASSABLE";
                break;
            case "1a":
                $result = "ASSEZ-BIEN";
                break;
            case "1b":
                $result = "BIEN";
                break;
            case "1t":
                $result = "TRES-BIEN";
                break;
            case "1e":
                $result = "EXCELLENT";
                break;
            case "A":
                $result = "5 POINTS";
                break;
            case "B":
                $result = "4 POINTS";
                break;
            case "C":
                $result = "3 POINTS";
                break;
            case "B":
                $result = "2 POINTS";
                break;
            case "A":
                $result = "1 POINTS";
                break;
        }
        return $result;
    }

    public function setOfficialExamResult(?string $officialExamResult): static
    {
        $this->officialExamResult = $officialExamResult;

        return $this;
    }

    public function isFinanceHolder(): ?bool
    {
        return $this->financeHolder;
    }
}

<?php

namespace App\Entity;

use App\Entity\Course;
use App\Entity\SchoolYear;
use App\Entity\Attribution;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;
use Doctrine\Persistence\ObjectManager;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 */
class Evaluation 
{
    use TimeStampable;

    public const NUM_ITEMS_PER_PAGE = 20;
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
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $abscent;

    /**
     * @ORM\Column(type="integer" , options={"default":0})
     */
    private $successH;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $successF;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $failluresH;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $failluresF;
    
    /**
     * @ORM\Column(type="float", options={"default":0})
     */
    private $mini;

    /**
     * @ORM\Column(type="float", options={"default":20})
     */
    private $maxi;

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
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;
    
   

    /**
     * @ORM\OneToMany(targetEntity=Mark::class, mappedBy="evaluation", orphanRemoval=true)
     */
    private $marks;

    public function __construct()
    {
        $this->marks = new ArrayCollection();
        $this->setFailluresF(0);
        $this->setFailluresH(0);
        $this->setSuccessF(0);
        $this->setSuccessH(0);
        $this->setAbscent(0);
        $this->createdAt= new \DateTime();
        $this->updatedAt= new \DateTime();
       
    }

    public function injectObjectManager(
        ObjectManager $objectManager,
        ClassMetadata $classMetadata
    ) {
        $this->em = $objectManager;
    }
    

    
 
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

        /**
    * Set successF
    *
    *
    * @return Evaluation
    */
    public function addSuccessF()
    {
        $this->successF++;

        return $this;
    }
  
       /**
    * Set successF
    *
    *
    * @return Evaluation
    */
    public function addSuccessH()
    {
        $this->successH++;

        return $this;
    }

    /**
    * Add failluresF
    *
    *
    * @return Evaluation
    */
    public function addFailluresH()
    {
        $this->failluresH++;

        return $this;
    }
  
     /**
    * Add Abscent
    *
    *
    * @return Evaluation
    */
    public function addAbscent()
    {
        $this->abscent++;

        return $this;
    }

      /**
     * Set failluresF
     *
     * @param integer $failluresF
     *
     * @return Evaluation
     */
    public function addFailluresF()
    {
        $this->failluresF++;

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



    /**
     * @return Collection|Mark[]
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function addMark(Mark $mark): self
    {
        if (!$this->marks->contains($mark)) {
            $this->marks[] = $mark;
            $mark->setEvaluation($this);
        }

        return $this;
    }

    public function removeMark(Mark $mark): self
    {
        if ($this->marks->removeElement($mark)) {
            // set the owning side to null (unless already changed)
            if ($mark->getEvaluation() === $this) {
                $mark->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getMini(): ?float
    {
        return $this->mini;
    }

    public function setMini(float $mini): static
    {
        $this->mini = $mini;

        return $this;
    }

    public function getMaxi(): ?float
    {
        return $this->maxi;
    }

    public function setMaxi(float $maxi): static
    {
        $this->maxi = $maxi;

        return $this;
    }
}

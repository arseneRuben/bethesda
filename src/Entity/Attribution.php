<?php
namespace App\Entity;
use App\Entity\Course;
use App\Entity\User;
use App\Entity\SchoolYear;
use App\Repository\AttributionRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribution
 *
 * @ORM\Table(name="attribution")
 * @ORM\Entity(repositoryClass=AttributionRepository::class)
 * @UniqueEntity(fields={"course", "schoolYear"}, message= "There is already an attribution othe this course to this teacher at this year")
 */
class Attribution {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    //put your code here
   
    /**
     * @ORM\ManyToOne(targetEntity=Course::class,inversedBy="attributions")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=false)
     */
    private $course;
    
     /**
     * @ORM\ManyToOne(targetEntity=User::class,inversedBy="attributions")
     * @ORM\JoinColumn(name="teacher_id", referencedColumnName="id", nullable=false)
     */
    private $teacher;

      /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class)
     * @ORM\JoinColumn(name="year_id", referencedColumnName="id", nullable=false)
     */
    private $schoolYear;
     /**
     * @var boolean
     *
     * @ORM\Column(name="head_teacher", type="boolean", options={"default":false})
     */
    private $headTeacher = false;

    
    public function setHeadTeacher($headTeacher)
    {
        $this->headTeacher = $headTeacher;
        return $this;
    }

    /**
     * Get enrolled
     *
     * @return boolean
     */
    public function getHeadTeacher()
    {
        return $this->headTeacher;
    }
   
    public function setTeacher(User $teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

   
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    
    public function setSchoolYear(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }

  
    public function setCourse(Course $course)
    {
        $this->course = $course;

        return $this;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function isHeadTeacher(): ?bool
    {
        return $this->headTeacher;
    }
}

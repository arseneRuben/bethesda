<?php

namespace App\Entity;

use App\Entity\Mark;
use App\Entity\Payment;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;
use App\Repository\StudentRepository;
use App\Entity\Traits\HasUploadableField;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 * @UniqueEntity(fields={"matricule"}, message="There is already an account with this matricule")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * 
 */
class Student
{
    use TimeStampable;
    use HasUploadableField;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="student_image", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize = "6024k",
     *     mimeTypes = {"image/bmp", "image/gif", "image/x-icon", "image/jpeg", "image/png", "image/svg+xml"},
     *     mimeTypesMessage = "Please upload a valid image(bmp,gif,jpg,jpeg,png,svg)"
     * )
     * 
     * @var File|null
     */
    private $imageFile;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $matricule;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="particular_disease", type="string", length=255, nullable=true)
     */
    private $particularDisease;

    /**
     * @var string
     *
     * @ORM\Column(name="father_name", type="string", length=255)
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="mother_name", type="string", length=255)
     */
    private $motherName;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_contact", type="string", length=255, nullable=true)
     */
    private $primaryContact;
    /**
     * @var string
     *
     * @ORM\Column(name="residence", type="string", length=255, nullable=true)
     */
    private $residence;

    /**
     * @var string
     *
     * @ORM\Column(name="secondary_contact", type="string", length=255, nullable=true)
     */
    private $secondaryContact;

    /**
     * @var string
     *
     * @ORM\Column(name="other_informations", type="text", nullable=true)
     */
    private $otherInformations;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=false)
     */
    private $birthday;

    /** @ORM\Column(name="gender", nullable=false, unique=false, length=10)
     * @Assert\Choice(
     * choices = { "0", "1" },
     * message = "précisez le sexe")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="birthplace", type="string", length=255)
     */
    private $birthplace;
    /**
     * @var boolean
     *
     * @ORM\Column(name="enrolled", type="boolean", options={"default":false})
     */
    private $enrolled = false;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="student")
     */
    private $subscriptions;
    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="student",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     * */
    private $payments;

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     *
     * @return string
     */
    public function __toString()
    {
        $lastname = (is_null($this->getLastName())) ? "" : $this->getLastName();
        $firstname = (is_null($this->getFirstName())) ? "" : $this->getFirstName();
        $matricule = (is_null($this->getMatricule())) ? "" : $this->getMatricule();

        return  $lastname . " " . $firstname . " " . $matricule;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Student
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }



    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
    }


    /**
     * Set particularDisease
     *
     * @param string $particularDisease
     *
     * @return Student
     */
    public function setParticularDisease($particularDisease)
    {
        $this->particularDisease = $particularDisease;

        return $this;
    }

    /**
     * Get particularDisease
     *
     * @return string
     */
    public function getParticularDisease()
    {
        return $this->particularDisease;
    }

    /**
     * Set fatherName
     *
     * @param string $fatherName
     *
     * @return Student
     */
    public function setFatherName($fatherName)
    {
        $this->fatherName = $fatherName;

        return $this;
    }

    /**
     * Get fatherName
     *
     * @return string
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * Set motherName
     *
     * @param string $motherName
     *
     * @return Student
     */
    public function setMotherName($motherName)
    {
        $this->motherName = $motherName;

        return $this;
    }

    /**
     * Get motherName
     *
     * @return string
     */
    public function getMotherName()
    {
        return $this->motherName;
    }

    /**
     * Set primaryContact
     *
     * @param string $primaryContact
     *
     * @return Student
     */
    public function setPrimaryContact($primaryContact)
    {
        $this->primaryContact = $primaryContact;

        return $this;
    }

    /**
     * Get primaryContact
     *
     * @return string
     */
    public function getPrimaryContact()
    {
        return $this->primaryContact;
    }

    /**
     * Set secondaryContact
     *
     * @param string $secondaryContact
     *
     * @return Student
     */
    public function setSecondaryContact($secondaryContact)
    {
        $this->secondaryContact = $secondaryContact;

        return $this;
    }

    /**
     * Get secondaryContact
     *
     * @return string
     */
    public function getSecondaryContact()
    {
        return $this->secondaryContact;
    }

    /**
     * Set otherInformations
     *
     * @param string $otherInformations
     *
     * @return Student
     */
    public function setOtherInformations($otherInformations)
    {
        $this->otherInformations = $otherInformations;

        return $this;
    }

    /**
     * Get otherInformations
     *
     * @return string
     */
    public function getOtherInformations()
    {
        return $this->otherInformations;
    }



    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Student
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthplace
     *
     * @param string $birthplace
     *
     * @return Student
     */
    public function setBirthplace($birthplace)
    {
        $this->birthplace = $birthplace;

        return $this;
    }

    /**
     * Get birthplace
     *
     * @return string
     */
    public function getBirthplace()
    {
        return $this->birthplace;
    }

    /**
     * Set level
     *
     * @param \AppBundle\Entity\Level $level
     *
     * @return Student
     */
    public function setLevel(Level $level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \AppBundle\Entity\Level
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Student
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get classRoom
     *
     * @return ClassRoom
     */
    public function getClassRoom(SchoolYear $year)
    {
        $subscribtion = $em->getRepository('AppBundle:Subscription')->findBy(array('schoolYear' => $year, 'student' => $std));
        return $subscribtion->getClassRoom();
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Student
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Student
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Student
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setEnrolled(false);
        $this->marks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new ArrayCollection();
    }


    public function addMark(Mark $mark)
    {
        $this->marks[] = $mark;

        return $this;
    }

    public function removeMark(Mark $mark)
    {
        $this->marks->removeElement($mark);
    }

    /**
     * Get marks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMarks()
    {
        return $this->marks;
    }

    /**
     * Set profileImagePath
     *
     * @param string $profileImagePath
     *
     * @return Student
     */
    public function setProfileImagePath($profileImagePath)
    {
        $this->profileImagePath = $profileImagePath;

        return $this;
    }

    /**
     * Get profileImagePath
     *
     * @return string
     */
    public function getProfileImagePath()
    {
        return $this->profileImagePath;
    }





    /**
     * Set residence
     *
     * @param string $residence
     *
     * @return Student
     */
    public function setResidence($residence)
    {
        $this->residence = $residence;

        return $this;
    }

    /**
     * Get residence
     *
     * @return string
     */
    public function getResidence()
    {
        return $this->residence;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setStudent($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getStudent() === $this) {
                $subscription->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * Set enrolled
     *
     * @param boolean $enrolled
     *
     * @return Student
     */
    public function setEnrolled($enrolled)
    {
        $this->enrolled = $enrolled;

        return $this;
    }

    /**
     * Get enrolled
     *
     * @return boolean
     */
    public function getEnrolled()
    {
        return $this->enrolled;
    }



    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }


    public function removePayment(Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    public function isEnrolled(): ?bool
    {
        return $this->enrolled;
    }
}

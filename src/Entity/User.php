<?php

namespace App\Entity;
use App\Entity\Domain;
use App\Entity\Course;
use App\Entity\SchoolYear;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;
use App\Repository\UserRepository;
use App\Entity\Traits\HasUploadableField;
use App\Repository\AttributionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"phoneNumber"}, message="There is already an account with this phone number")
 * @UniqueEntity(fields={"numCni"}, message="There is already an account with this cni number")
 * @ORM\HasLifecycleCallbacks
 * 
 */
class User implements UserInterface//, PasswordAuthenticatedUserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Please enter your email address")
     * @Assert\NotBlank(message="Please enter a valid  email address")
     */
    private $email;
     



    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
      /**
     * @Assert\EqualTo( value="password",
     * message = " Le mot de passe et le mot de passe de verification doivent etre les memes ")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

      /**
     * @ORM\OneToMany(targetEntity=Email::class, mappedBy="sender")
     */
    private $emails;
  
    


   

      /**
     * @ORM\Column(name="avatarPath", type="string", length=255, nullable=true)
     */
    protected $avatarPath;
   



    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=false)
     */
    protected $phoneNumber;

    /** 
     *  @var string
     * @ORM\Column(name="gender", nullable=true, unique=false, length=10) 
     * @Assert\Choice(choices = { 0, 1 },  message = "précisez le sexe")
     */
    protected $gender;

    /**
     * @var \Date
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    protected $birthday;
    /**
     * @var string
     *
     * @ORM\Column(name="birthplace", type="string", length=255, nullable=true)
     */
    private $birthplace;
    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
     */
    protected $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    protected $location;
    /** @ORM\Column(name="region", nullable=true, unique=false, length=10) 
     * @Assert\Choice(
     * choices = { "Adamaoua", "Centre" ,"Est", "Extrême-Nord" ,"Littoral", "Nord", "Nord-Ouest" ,"Ouest", "Sud", "Sud-Ouest"},
     * message = "précisez votre region d'origine")
     */
    protected $region;
      /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     */
    protected $department;

    /** @ORM\Column(name="academicLevel", nullable=true, unique=false, length=10) 
     * @Assert\Choice(
     * choices = { "BAC", "LICENCE" ,"DIP1", "DIP2" ,"MASTER", "DOCTORAT"},
     * message = "précisez le niveau académique")
     */
    protected $academicLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="numCni", type="string", length=255, nullable=true, unique=false)
     */
    protected $numCni;

  



    /** @ORM\Column(name="status", nullable=true, unique=false, length=10) 
     * @Assert\Choice(
     * choices = {"ELEVE","ADMIN", "PROF", "FINANCE", "PRINCIPAL", "PREFET"},
     * * message = "précisez votre statu dans ISBB")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity=Domain::class, inversedBy="teachers")
     */
    private $domain;

    
      /**
     * @ORM\OneToMany(targetEntity=Domain::class, mappedBy="headOfDepartment")
     */
    private $headOfDepartementOf;
    
    /**
     * @ORM\OneToMany(targetEntity=Attribution::class, mappedBy="teacher")
     */
    private $attributions;
    /**
     * @ORM\OneToMany(targetEntity=MainTeacher::class, mappedBy="teacher")
     */
    private $mainTeachers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $securityQuestion;

    /**
     * @ORM\Column(type="string", length=255,options={"default": "bethesda"}, nullable=true)
     */
    private $securityAnswer;

    public function getSecurityQuestion(): ?string
    {
        return $this->securityQuestion;
    }

    public function setSecurityQuestion(string $securityQuestion): self
    {
        $this->securityQuestion = $securityQuestion;

        return $this;
    }

    public function getSecurityAnswer(): ?string
    {
        return $this->securityAnswer;
    }

    public function setSecurityAnswer(string $securityAnswer): self
    {
        $this->securityAnswer = $securityAnswer;

        return $this;
    }

  
   
    public function getAvatar(int $size = 50): ?string
    {
        return "https://www.gravatar.com/avatar/". md5(strtolower(trim($this->getEmail())))."/?s=".$size;
    }

    public function __construct()
    {
        
        $this->emails = new ArrayCollection();
        $this->fullTeacherOf = new ArrayCollection();
        $this->attributions = new ArrayCollection();
        $this->headOfDepartementOf = new ArrayCollection();
        $this->mainTeachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $imageName): self
    {
        $this->avatarPath = $imageName;

        return $this;
    }

   



    public function __toString() {
        $username = ( is_null($this->getFullName())) ? "" : $this->getFullName();
        return $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';
        //$roles[] = 'ROLE_ADMIN';
        switch ($this->getStatus()) {
            case "PROF":
                $roles[] = 'ROLE_USER';
		$roles[] = 'ROLE_PROF';
            case "ADMIN":
                $roles[] = 'ROLE_USER';
                $roles[] = 'ROLE_ADMIN';
            break;
            case "DISCIPLINE":
                $roles[] = 'ROLE_USER';
                $roles[] = 'ROLE_DISCIPLINE';
            break;
            case "FINANCIAL":
                $roles[] = 'ROLE_USER';
                $roles[] = 'ROLE_FINANCIAL';
            break;
            default:
                $roles[] = 'ROLE_USER';
            break;
          }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function addRole($role): self
    {
        array_push($this->roles, $role);
        $this->setRoles(array_unique($this->roles));
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function toggleIsVerified(): self
    {
         $this->isVerified = !$this->isVerified;
         return $this;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phone): self
    {
        $this->phoneNumber = $phone;

        return $this;
    }

     /**
     * 
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($email) {
        if (!empty($email))
            $this->status = $email;

        return $this;
    }

      /**
     * Set birthplace
     *
     * @param string $birthplace
     *
     * @return User
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
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
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
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender) {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

     /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return User
     */
    public function setNationality($nationality) {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality() {
        return $this->nationality;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return User
     */
    public function setLocation($location) {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set academicLevel
     *
     * @param string $academicLevel
     *
     * @return User
     */
    public function setAcademicLevel($academicLevel) {
        $this->academicLevel = $academicLevel;

        return $this;
    }

    /**
     * Get academicLevel
     *
     * @return string
     */
    public function getAcademicLevel() {
        return $this->academicLevel;
    }

    /**
     * Set numCni
     *
     * @param string $numCni
     *
     * @return User
     */
    public function setNumCni($numCni) {
        $this->numCni = $numCni;

        return $this;
    }

    /**
     * Get numCni
     *
     * @return string
     */
    public function getNumCni() {
        return $this->numCni;
    }

     /**
     * Set domain
     *
     * @param \App\Entity\Domain $domain
     *
     * @return User
     */
    public function setDomain(Domain $domain = null) {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return \App\Entity\Domain
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @return Collection|ClassRoom[]
     */
    public function getFullTeacherOf(): Collection
    {
        return $this->fullTeacherOf;
    }

    public function addFullTeacherOf(ClassRoom $fullTeacherOf): self
    {
        if (!$this->fullTeacherOf->contains($fullTeacherOf)) {
            $this->fullTeacherOf[] = $fullTeacherOf;
            $fullTeacherOf->setFullTeacher($this);
        }

        return $this;
    }

    public function removeFullTeacherOf(ClassRoom $fullTeacherOf): self
    {
        if ($this->fullTeacherOf->removeElement($fullTeacherOf)) {
            // set the owning side to null (unless already changed)
            if ($fullTeacherOf->getFullTeacher() === $this) {
                $fullTeacherOf->setFullTeacher(null);
            }
        }

        return $this;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->username_canonical;
    }

    public function setUsernameCanonical(string $username_canonical): self
    {
        $this->username_canonical = $username_canonical;

        return $this;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->email_canonical;
    }

    public function setEmailCanonical(string $email_canonical): self
    {
        $this->email_canonical = $email_canonical;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $last_login): self
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeImmutable
    {
        return $this->password_requested_at;
    }

    public function setPasswordRequestedAt(?\DateTimeImmutable $password_requested_at): self
    {
        $this->password_requested_at = $password_requested_at;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
            $email->setSender($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        if ($this->emails->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getSender() === $this) {
                $email->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Attribution[]
     */
    public function getAttributions(): Collection
    {
        return $this->attributions;
    }

    /**
     * list of courses assigned to a teacher during a given year
     */
    public function getCourses(SchoolYear $year)
    {
        $courses = [];
        foreach($this->attributions as $attribution){
            if($attribution->getSchoolYear()==$year){
                $courses[] = $attribution->getCourse();
            }
        }
        return $courses;
    }

    public function addAttribution(Attribution $attribution): self
    {
        if (!$this->attributions->contains($attribution)) {
            $this->attributions[] = $attribution;
            $attribution->setTeacher($this);
        }

        return $this;
    }

    public function removeAttribution(Attribution $attribution): self
    {
        if ($this->attributions->removeElement($attribution)) {
            // set the owning side to null (unless already changed)
            if ($attribution->getTeacher() === $this) {
                 
            }
        }

        return $this;
    }

    public function getUserIdentifier() {
      return $this->getEmail();
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

 

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, Domain>
     */
    public function getHeadOfDepartementOf(): Collection
    {
        return $this->headOfDepartementOf;
    }

    public function addHeadOfDepartementOf(Domain $headOfDepartementOf): static
    {
        if (!$this->headOfDepartementOf->contains($headOfDepartementOf)) {
            $this->headOfDepartementOf->add($headOfDepartementOf);
            $headOfDepartementOf->setHeadOfDepartment($this);
        }

        return $this;
    }

    public function removeHeadOfDepartementOf(Domain $headOfDepartementOf): static
    {
        if ($this->headOfDepartementOf->removeElement($headOfDepartementOf)) {
            // set the owning side to null (unless already changed)
            if ($headOfDepartementOf->getHeadOfDepartment() === $this) {
                $headOfDepartementOf->setHeadOfDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MainTeacher>
     */
    public function getMainTeachers(): Collection
    {
        return $this->mainTeachers;
    }

    public function addMainTeacher(MainTeacher $mainTeacher): static
    {
        if (!$this->mainTeachers->contains($mainTeacher)) {
            $this->mainTeachers->add($mainTeacher);
            $mainTeacher->setTeacher($this);
        }

        return $this;
    }

    public function removeMainTeacher(MainTeacher $mainTeacher): static
    {
        if ($this->mainTeachers->removeElement($mainTeacher)) {
            // set the owning side to null (unless already changed)
            if ($mainTeacher->getTeacher() === $this) {
                $mainTeacher->setTeacher(null);
            }
        }

        return $this;
    }

  

}

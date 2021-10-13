<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Traits\HasUploadableField;
use App\Entity\Traits\TimeStampable;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @Vich\Uploadable
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
     */
    private $email;


    /**
     * @ORM\Column(type="json")
     */
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
     * Date/Time of the last activity
     *
     * @var \Datetime
     * @ORM\Column(name = "lastactivityat", type = "datetime",  nullable=true)
     */
    protected $lastActivityAt;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="avatarPath")
     * @Assert\Image(maxSize="8M")
     * 
     * @var File|null
     */
    private $imageFile;
      /**
     * @ORM\Column(name="avatarPath", type="string", length=255, nullable=true)
     */
    protected $avatarPath;
   

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    protected $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=false)
     */
    protected $phoneNumber;

    /** @ORM\Column(name="gender", nullable=true, unique=false, length=10) 
     * @Assert\Choice(
     * choices = { "M", "F" },
     * message = "précisez le sexe")
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

    /**
     * @ORM\Column(type="integer", length=6, options={"default":0})
     */
    protected $loginCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $firstLogin;

    /** @ORM\Column(name="status", nullable=true, unique=false, length=10) 
     * @Assert\Choice(
     * choices = {"ELEVE", "PROF", "FINANCE", "PRINCIPAL", "PREFET"},
     * * message = "précisez votre statu dans ISBB")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity=Domain::class, inversedBy="users")
     */
    private $domain;

    /**
     * @ORM\OneToMany(targetEntity=ClassRoom::class, mappedBy="fullTeacher")
     */
    private $fullTeacherOf;

    
   
    public function getAvatar(int $size = 50): ?string
    {
        return "https://www.gravatar.com/avatar/". md5(strtolower(trim($this->getEmail())))."/?s=".$size;
    }

    public function __construct()
    {
        
        $this->emails = new ArrayCollection();
        $this->fullTeacherOf = new ArrayCollection();
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

   


    public function getFullName (): ?string
    {
        return $this->fullName ;
    }

     /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($numCni) {
        $this->fullName = $numCni;

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
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_ADMIN';

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
     * Get firstName
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
    public function setDomain(\App\Entity\Domain $domain = null) {
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
}

<?php

namespace App\Entity;

use App\Entity\SchoolYear;
use App\Entity\Subscription;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClassRoomRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\Collection;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ClassRoomRepository::class)
 * @UniqueEntity(fields={"name"}, message= "There is already a classroom  with this name")
 */
class ClassRoom
{
    public const NUM_ITEMS_PER_PAGE = 3;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Cette classe est-elle une classe d'examen?
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $apc;


    /**
     * @ORM\OneToMany(targetEntity=Module::class, mappedBy="room", orphanRemoval=true)
     */
    private $modules;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="rooms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="fullTeacherOf")
     * @ORM\JoinColumn(nullable=true)
     */
    private $fullTeacher;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="classRoom")
     */
    private $subscriptions;




    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    public function __toString()
    {
        $name = (is_null($this->getName())) ? "" : $this->getName();
        $level = (is_null($this->getLevel())) ? "" : $this->getLevel();
        return (string) ($level . "/" . $name);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getApc(): ?bool
    {
        return $this->apc;
    }

    public function setApc(bool $apc): self
    {
        $this->apc = $apc;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setRoom($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getRoom() === $this) {
                $module->setRoom(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getFullTeacher(): ?User
    {
        return $this->fullTeacher;
    }

    public function setFullTeacher(?User $fullTeacher): self
    {
        $this->fullTeacher = $fullTeacher;
        return $this;
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
            $subscription->setClassRoom($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getClassRoom() === $this) {
                $subscription->setClassRoom(null);
            }
        }

        return $this;
    }

    public function isApc(): ?bool
    {
        return $this->apc;
    }
}

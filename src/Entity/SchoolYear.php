<?php

namespace App\Entity;

use App\Entity\Traits\Period;
use App\Repository\SchoolYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass=SchoolYearRepository::class)
 *  @UniqueEntity(fields={"code"}, message= "There is already a classroom  with this code")
 */
class SchoolYear
{
    use Period;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @var int
     *
     * @ORM\Column(name="reductionRrime", type="integer")
     */
    private $reductionPrime;





    public function __toString()
    {
        $name = (is_null($this->getWording())) ? "" : $this->getWording();
        return (string) ($name);
    }

    public function unable()
    {
        $this->setActivated(true);
        if (count($this->getQuaters()) > 0)
            $this->getQuaters()[0]->unable();
    }

    public function disable()
    {
        $this->setActivated(false);
        foreach ($this->getQuaters() as $quater) {
            $quater->disable();
        }
    }






    /**
     * @ORM\OneToMany(targetEntity=Quater::class, mappedBy="schoolYear", orphanRemoval=true, cascade={"persist"})
     */
    private $quaters;

    /**
     * @ORM\OneToMany(targetEntity=SettingsPayments::class, mappedBy="schoolYear", orphanRemoval=true)
     */
    private $settingsPayments;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="schoolYear")
     */
    private $subscriptions;



    public function __construct()
    {
        $this->quaters = new ArrayCollection();
        $this->settingsPayments = new ArrayCollection();
        $this->activated = true;
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Set reductionPrime
     *
     * @param integer $reductionPrime
     *
     * @return SchoolYear
     */
    public function setReductionPrime($reductionPrime)
    {
        $this->reductionPrime = $reductionPrime;

        return $this;
    }

    /**
     * Get reductionPrime
     *
     * @return integer
     */
    public function getReductionPrime()
    {
        return $this->reductionPrime;
    }

    /**
     * @return Collection|Quater[]
     */
    public function getQuaters(): Collection
    {
        return $this->quaters;
    }

    public function addQuater(Quater $quater): self
    {
        if (!$this->quaters->contains($quater)) {
            $this->quaters[] = $quater;
            $quater->setSchoolYear($this);
        }

        return $this;
    }

    public function removeQuater(Quater $quater): self
    {
        if ($this->quaters->removeElement($quater)) {
            // set the owning side to null (unless already changed)
            if ($quater->getSchoolYear() === $this) {
                $quater->setSchoolYear(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SettingsPayments[]
     */
    public function getSettingsPayments(): Collection
    {
        return $this->settingsPayments;
    }

    public function addSettingsPayment(SettingsPayments $settingsPayment): self
    {
        if (!$this->settingsPayments->contains($settingsPayment)) {
            $this->settingsPayments[] = $settingsPayment;
            $settingsPayment->setSchoolYear($this);
        }

        return $this;
    }

    public function removeSettingsPayment(SettingsPayments $settingsPayment): self
    {
        if ($this->settingsPayments->removeElement($settingsPayment)) {
            // set the owning side to null (unless already changed)
            if ($settingsPayment->getSchoolYear() === $this) {
                $settingsPayment->setSchoolYear(null);
            }
        }

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
            $subscription->setSchoolYear($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSchoolYear() === $this) {
                $subscription->setSchoolYear(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Entity\Traits\Period;
use App\Entity\PaymentPlan;
use App\Repository\SchoolYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $registrationDeadline;
    /**
     * @var int
     *
     * @ORM\Column(name="rate", type="integer")
     */
    private $rate;





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
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="schoolYear")
     */
    private $subscriptions;
    /**
     * @ORM\OneToOne(targetEntity=PaymentPlan::class, mappedBy="schoolYear")
     */
    private $paymentPlan;



    public function __construct()
    {
        $this->quaters = new ArrayCollection();
        $this->activated = true;
        $this->subscriptions = new ArrayCollection();
        $this->paymentPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Set rate
     *
     * @param integer $reductionPrime
     *
     * @return SchoolYear
     */
    public function setRate($reductionPrime)
    {
        $this->rate = $reductionPrime;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
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
     * Get amountofTuition
     *
     * @param ClassRoom $room
     *
     * @return integer
     */
    public function amountofTuition(ClassRoom $room) {
        
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

    public function getPaymentPlan(): ?PaymentPlan
    {
        return $this->paymentPlan;
    }

    public function setPaymentPlan(?PaymentPlan $paymentPlan): static
    {
        // unset the owning side of the relation if necessary
        if ($paymentPlan === null && $this->paymentPlan !== null) {
            $this->paymentPlan->setSchoolYear(null);
        }

        // set the owning side of the relation if necessary
        if ($paymentPlan !== null && $paymentPlan->getSchoolYear() !== $this) {
            $paymentPlan->setSchoolYear($this);
        }

        $this->paymentPlan = $paymentPlan;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(?\DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

   
    


}

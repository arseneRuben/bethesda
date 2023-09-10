<?php

namespace App\Entity;

use App\Entity\SchoolYear;
use App\Entity\ClassRoom; 
use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimeStampable;

/**
 * PaymentPlan
 *
 * @ORM\Table(name="payment_plan")
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class PaymentPlan
{
    use TimeStampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numberOfInstallments", type="integer")
     */
    private $numberOfInstallments;

    /**
     * @ORM\ManyToOne(targetEntity=SchoolYear::class)
     * @ORM\JoinColumn(name="school_year_id", referencedColumnName="id", nullable=true)
     */
    private $schoolYear;


    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class) 
     * @ORM\JoinColumn(name="classRoom_id", referencedColumnName="id", nullable=true)
     */
    private $classRoom; 

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="paymentPlan")
     */
    private $payments;

    
}

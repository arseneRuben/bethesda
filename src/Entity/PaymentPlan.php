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
     * @ORM\ManyToOne(targetEntity=Student::class)
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=Class::class) // Lien vers l'entité Class
     * @ORM\JoinColumn(name="class_id", referencedColumnName="id", nullable=true)
     */
    private $class; // Ajout du champ class

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="paymentPlan")
     */
    private $payments;

    
}

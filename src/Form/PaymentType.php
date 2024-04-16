<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\StudentRepository;
use App\Service\SchoolYearService;



class PaymentType extends AbstractType
{
    private StudentRepository $stdRepo;
    private SchoolYearService      $schoolYearService;

    public function __construct( SchoolYearService $schoolYearService,StudentRepository $stdRepo)
    {
       
        $this->schoolYearService = $schoolYearService;
        $this->stdRepo = $stdRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $year = $this->schoolYearService->sessionYearById();

        $builder
            ->add('subscription', CheckboxType::class, array('label' => 'Inscription ?', 'data' => false))
            ->add('amount')
            ->add('student', EntityType::class, array('class' => Student::class,  'placeholder' => 'Eleve', 'required' => true,  'choices' => $this->stdRepo->findEnrolledStudentsThisYear2()))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}

<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\QuaterRepository;
use App\Repository\StudentRepository;
use App\Entity\Quater;
use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Filter\PaymentSearch;
use App\Service\SchoolYearService;

class PaymentSearchType extends AbstractType
{
    private SchoolYearService $schoolYearService;
    private StudentRepository $stdRepo;

    public function __construct( StudentRepository $stdRepo,SchoolYearService $schoolYearService)
    {
        $this->schoolYearService = $schoolYearService;
        $this->stdRepo = $stdRepo;

    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('startDate', DateType::class, [
            'widget' => 'single_text',
            'label' => false,
            'required' => false,
            'placeholder' => 'Debut',

        ])

        ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            'label' => false,
            'required' => false,
            

        ])
        ->add('quater', EntityType::class, [
            'class' => Quater::class,
            'required' => false,
            'label' => false,
            'placeholder' => 'Filtrer Selon le trimestre',
            'query_builder' => function (QuaterRepository $repository) {
                return $repository->createQueryBuilder('q')->leftJoin('q.schoolYear', 'sc')->where('sc.id = :id')->setParameter('id', $this->schoolYearService->sessionYearById()->getId())->add('orderBy', 'q.id');
            }
        ])
            ->add('room', EntityType::class, [
                'class' => ClassRoom::class,
                'required' => false,
                'label' => false,
                'placeholder' => 'Filtrer Selon la classe'
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,  
                'required' => false,  
                'label' => false,
                'placeholder' => 'Filtrer Selon l\'eleve',
                'choices' => $this->stdRepo->findEnrolledStudentsThisYear2()])
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PaymentSearch::class,
            'method' => 'get',
            'csrf_protection' => false

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_filter';
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

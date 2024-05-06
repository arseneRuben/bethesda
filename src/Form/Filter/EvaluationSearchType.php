<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\SequenceRepository;
use App\Entity\Course;
use App\Entity\Sequence;
use App\Entity\ClassRoom;
use App\Filter\EvaluationSearch;
use App\Service\SchoolYearService;


class EvaluationSearchType extends AbstractType
{
    private SchoolYearService $schoolYearService;

    public function __construct(SchoolYearService $schoolYearService)
    {
        $this->schoolYearService = $schoolYearService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('course', EntityType::class, [
                'class' => Course::class,
                'required' => false,
                'label' => false,
                'placeholder' => 'Filtrer le cours',
                'attr' => [
                    'placeholder' => 'Matiere'
                ]
            ])
            ->add('sequence', EntityType::class, [
                'class' => Sequence::class,
                'required' => false,
                'label' => false,

                'placeholder' => 'Filtrer Selon la sequence',
                'query_builder' => function (SequenceRepository $repository) {
                    return $repository->createQueryBuilder('s')->leftJoin('s.quater', 'q')->leftJoin('q.schoolYear', 'sc')->where('sc.id = :id')->setParameter('id', $this->schoolYearService->sessionYearById()->getId())->add('orderBy', 's.id');
                }
            ])
            ->add('room', EntityType::class, [
                'class' => ClassRoom::class,
                'required' => false,
                'label' => false,
                'placeholder' => 'Filtrer Selon la classe'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EvaluationSearch::class,
            'method' => 'get',
            'csrf_protection' => false

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'evaluation_filter';
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

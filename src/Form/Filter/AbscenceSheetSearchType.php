<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\QuaterRepository;

use App\Entity\Quater;
use App\Entity\ClassRoom;
use App\Filter\AbscenceSearch;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Service\SchoolYearService;


class AbscenceSheetSearchType extends AbstractType
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
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AbscenceSearch::class,
            'method' => 'get',
            'csrf_protection' => false

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'abscence_filter';
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

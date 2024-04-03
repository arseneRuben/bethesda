<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\SequenceRepository;
use App\Entity\Sequence;
use App\Entity\ClassRoom;
use App\Filter\PaymentSearch;

class PaymentSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('sequence', EntityType::class, [
                'class' => Sequence::class,
                'required' => false,
                'label' => false,

                'placeholder' => 'Filtrer Selon la sequence',
                'query_builder' => function (SequenceRepository $repository) {
                    return $repository->createQueryBuilder('s')->leftJoin('s.quater', 'q')->leftJoin('q.schoolYear', 'sc')->where('sc.activated = :rep')->setParameter('rep', true)->add('orderBy', 's.id');
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

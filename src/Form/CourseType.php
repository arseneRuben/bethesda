<?php

namespace App\Form;
use App\Entity\Course;

use App\Entity\Domain;
use App\Entity\Module;
use App\Repository\DomainRepository;
use App\Repository\ModuleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('wording')
            ->add('coefficient')
            ->add('code')
            ->add('domain', EntityType::class, array('class' => Domain::class, 'placeholder' => 'Département', 'required' => true,  'query_builder' => function (DomainRepository $repository)
            {return $repository->createQueryBuilder('d')->add('orderBy', 'd.name');} ))
            ->add('module',EntityType::class, array('class' => Module::class, 'placeholder' => 'Module de cours', 'required' => true,  'query_builder' => function (ModuleRepository $repository)
            {return $repository->createQueryBuilder('m')->add('orderBy', 'm.room');} ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Course::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'course';
    }
}

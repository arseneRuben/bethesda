<?php

namespace App\Form;
use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Entity\Subscription;
use App\Repository\ClassRoomRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Subscription2Type extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('student', EntityType::class, array('class' => Student::class, 'label' => 'Elève', 'required' => true))
                ->add('classRoom', EntityType::class,  array('class' => ClassRoom::class, 'label' => 'Classe', 'required' => true,'query_builder' => function (ClassRoomRepository $repository) {
                    return $repository->createQueryBuilder('c')->leftJoin('c.level', 'l')->add('orderBy', 'l.id');
                } ))
                ->add('schoolYear', EntityType::class, array('class' => SchoolYear::class, 'label' => 'Année Scolaire', 'required'=> true))
                ->add('financeHolder', ChoiceType::class, array(
                    'constraints' => new Assert\NotBlank(),
                    'choices' => array(
                        '0' => 'NON',
                        '1' => 'OUI',
                    ), 'label' => 'Conditions financières'));
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Subscription::class,
            'entityManager' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'subscription';
    }
}

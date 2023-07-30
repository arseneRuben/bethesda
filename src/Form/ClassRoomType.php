<?php

namespace App\Form;

use App\Entity\ClassRoom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('apc', CheckboxType::class, array('label' => 'Classe d\'examen ?'))
            ->add('level')
            ->add('fullTeacher');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClassRoom::class,
        ]);
    }
}

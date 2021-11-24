<?php

namespace App\Form;

use App\Entity\Quater;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SchoolYearRepository;

class QuaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('wording')
        ->add('code')
        ->add('startDate', DateType::class, [
            'label' => 'Date de début',
            'widget' => 'single_text',
            'required' => true,
             'input' => 'datetime',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker'],
            'format' => 'yyyy-MM-dd',
            'constraints' => new Assert\Date(),
            'constraints' => new Assert\NotBlank(),
            'trim' => true])
        ->add('endDate', DateType::class, [
            'label' => 'Date de fin',
            'widget' => 'single_text',
            'required' => true,
             'input' => 'datetime',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker'],
            'format' => 'yyyy-MM-dd',
            'constraints' => new Assert\Date(),
            'constraints' => new Assert\NotBlank(),
            'trim' => true])
        ->add('activated')
        ->add('schoolYear');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quater::class,
        ]);
    }
}

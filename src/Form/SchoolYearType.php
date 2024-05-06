<?php

namespace App\Form;

use App\Entity\SchoolYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SchoolYearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code', TextType::class, [
            'label' => 'Code',
            'required' => true,
            'constraints' => new Assert\NotBlank(),
            'trim' => true
        ])
        ->add('wording', TextType::class, [
            'label' => 'Année Scolaire',
            'required' => true,
            'constraints' => new Assert\NotBlank(),
            'trim' => true
        ])->add('startDate', DateType::class, [
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
           
            ->add('rate', NumberType::class, [
                'label' => 'Montant du taux d\'inscription',
                'required' => false,
                'trim' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SchoolYear::class,
        ]);
    }
}

<?php
// src/Form/PaymentPlanType.php

namespace App\Form;

use App\Entity\PaymentPlan;
use App\Entity\SchoolYear;
use App\Entity\ClassRoom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          
            ->add('schoolYear', EntityType::class, [
                'class' => SchoolYear::class,
                'placeholder' => 'Choisir l\'année scolaire',
                'required' => true,
            ])
          
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaymentPlan::class,
        ]);
    }
}

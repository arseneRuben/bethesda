<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('subject', ChoiceType::class, array(
            'attr' => ['class' => 'form-control'],
            'choices' => array(
                'RENSEIGNEMENT' => 'RENSEIGNEMENT',
                'TEMOIGNAGE' => 'TEMOIGNAGE',
                'SUGGESTION' => 'SUGGESTION',
                'DON' => 'FAIRE UN DON',
                'PLAINTE' => 'PLAINTE'
               
            ),'label' => 'Objet'))
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}

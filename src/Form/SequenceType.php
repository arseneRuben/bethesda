<?php

namespace App\Form;

use App\Entity\Quater;
use App\Entity\Sequence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;;

use App\Repository\QuaterRepository;

class SequenceType extends AbstractType
{
    private $repo;
    public function __construct(QuaterRepository $scRepo)
    {
        $this->repo = $scRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording',   ChoiceType::class, [
                'choices' => [
                    'Principales' => [
                        'Session 1' => 'session1',
                        'Session 2' => 'session2',
                        'Session 3' => 'session3',
                        'Session 4' => 'session4',
                        'Session 5' => 'session5',
                        'Session 6' => 'session6',
                    ],
                    'Optionnelles' => [
                        'Session 7' => 'session7',
                        'Session 8' => 'session8',
                    ],
                ],
            ], ['title' => 'Veuillez entrer une valeur de session valide (session1 à session6)'])
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
                'trim' => true
            ])
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
                'trim' => true
            ])
            ->add('activated')

            ->add('validationDate', DateType::class, [
                'label' => 'Date de validation des résultalts sequentiels',
                'widget' => 'single_text',
                'required' => true,
                'input' => 'datetime',
                'html5' => true,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'yyyy-MM-dd',
                'constraints' => new Assert\Date(),
                'constraints' => new Assert\NotBlank(),
                'trim' => true
            ])
            ->add('quater',  EntityType::class, [
                'class' => Quater::class,
                'data' => $options['activated_quater'], // Spécifiez la valeur par défaut ici
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Sequence',
            'activated_quater' => $this->repo->findOneBy(array("activated" => true))

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sequence';
    }
}

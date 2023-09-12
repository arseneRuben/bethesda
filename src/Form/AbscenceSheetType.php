<?php

namespace App\Form;

use App\Entity\AbscenceSheet;
use App\Entity\ClassRoom;
use App\Entity\Sequence;
use App\Repository\SequenceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbscenceSheetType extends AbstractType
{
    private $repo;
    public function __construct(SequenceRepository $scRepo)
    {
        $this->repo = $scRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sequence', EntityType::class, array('class' => Sequence::class, 'data' => $options['activated_sequence'], 'placeholder' => 'Choisir la séquence', 'required' => true, 'query_builder' => function (SequenceRepository $repository) {
                return $repository->createQueryBuilder('s')->leftJoin('s.quater', 'q')->leftJoin('q.schoolYear', 'sc')->where('sc.activated = :rep')->setParameter('rep', true)->add('orderBy', 's.id');
            }))
            ->add('classRoom', EntityType::class, array('placeholder' => 'Choisir une classe', 'class' => ClassRoom::class, 'required' => true))
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbscenceSheet::class,
            'activated_sequence' => $this->repo->findOneBy(array("activated" => true)),

        ]);
    }
}

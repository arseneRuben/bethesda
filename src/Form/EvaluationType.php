<?php

namespace App\Form;

use App\Entity\Sequence;
use App\Entity\ClassRoom;
use App\Entity\Evaluation;
use App\Repository\SequenceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EvaluationType extends AbstractType
{
    private $repo;
    public function __construct(SequenceRepository $scRepo)
    {
        $this->repo = $scRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
        ->add('sequence', EntityType::class, array('class' => Sequence::class,'data' => $options['activated_sequence'], 'placeholder' => 'Choisir la séquence', 'required' => true,'query_builder' => function (SequenceRepository $repository) {
                return $repository->createQueryBuilder('s')->leftJoin('s.quater', 'q')->leftJoin('q.schoolYear', 'sc') ->where('sc.activated = :rep')->setParameter('rep', true)->add('orderBy', 's.id');
             } ))
         ->add('classRoom', EntityType::class, array('placeholder' => 'Choisir une classe','class' => ClassRoom::class, 'required' => true ))
         ->add('competence',TextType::class, ['label' => 'Competence','required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
            'activated_sequence' => $this->repo->findOneBy(array("activated" => true))

        ]);
    }
}

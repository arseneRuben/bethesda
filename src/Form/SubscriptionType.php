<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Entity\Subscription;
use App\Repository\StudentRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SubscriptionType extends AbstractType
{

    private $repo;
    private $scRepo;
    private $stdRepo;
    private $year;
    public function __construct(SubscriptionRepository $repo, SchoolYearRepository $scRepo, StudentRepository $stdRepo)
    {
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->stdRepo = $stdRepo;
        $this->year = $this->scRepo->findOneBy(array("activated" => true));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('student', EntityType::class, array('class' => Student::class,  'placeholder' => 'Eleve', 'required' => true,  'choices' => $this->stdRepo->findNotEnrolledStudentsThisYear2($this->year)))
            ->add('classRoom', EntityType::class, array('class' => ClassRoom::class, 'label' => 'Classe', 'required' => true))
            ->add('schoolYear', EntityType::class, array('class' => SchoolYear::class, 'label' => 'Année Scolaire', 'required' => true,  'query_builder' => function (SchoolYearRepository $repository) {
                return $repository->createQueryBuilder('s')->where('s.activated=:ac')->setParameter('ac', true);
            }))
            ->add('officialExamResult', ChoiceType::class, array(
                'data' => $options['defaultOfficialExamResult'],
                'constraints' => new Assert\NotBlank(),
                'choices' => array(
                    'ECHEC'         => '0',
                    'PASSABLE'      => '1p',
                    'ASSEZ-BIEN'    => '1a',
                    'BIEN'          => '1b',
                    'TRES-BIEN'     => '1t',
                    'EXCELLENT'     => '1e',
                    '5 POINTS'      => 'A',
                    '4 POINTS'      => 'B',
                    '3 POINTS'      => 'C',
                    '2 POINTS'      => 'D',
                    '1 POINTS'      => 'E',
                ), 'label' => 'Resultat a l\'examen officiel'
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Subscription::class,
            'entityManager' => null,
            'defaultOfficialExamResult' => '1p',
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

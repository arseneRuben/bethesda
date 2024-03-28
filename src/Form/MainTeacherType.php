<?php

namespace App\Form;

use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Repository\UserRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\MainTeacherRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MainTeacherType extends AbstractType
{

    private $repo;
    private $scRepo;
    private $userRepo;
    private $year;
    public function __construct(MainTeacherRepository $repo, SchoolYearRepository $scRepo, UserRepository $userRepo)
    {
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->userRepo = $userRepo;
        $this->year = $this->scRepo->findOneBy(array("activated" => true));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('teacher', EntityType::class, array('class' => User::class,  'placeholder' => 'Enseignant', 'required' => true,  'choices' => $this->userRepo->findNotYetHeadTeacher($this->year)))
            ->add('classRoom', EntityType::class, array('class' => ClassRoom::class, 'label' => 'Classe', 'required' => true))
            ->add('schoolYear', EntityType::class, array('class' => SchoolYear::class, 'label' => 'Année Scolaire', 'required' => true,  'query_builder' => function (SchoolYearRepository $repository) {
                return $repository->createQueryBuilder('s')->where('s.activated=:ac')->setParameter('ac', true);
            }))
;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MainTeacher::class,
            'entityManager' => null        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mainteacher';
    }
}

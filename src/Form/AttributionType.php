<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Attribution;
use App\Repository\UserRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\AttributionRepository;
use App\Repository\CourseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;


class AttributionType extends AbstractType
{
    private $em;
    private $year;
    private $repo;
    private $scRepo;
    private $session;

    public function __construct(  EntityManagerInterface $em,AttributionRepository $repo, SessionInterface $session, SchoolYearRepository $scRepo, CourseRepository $crsRepo)
    {
        $this->em = $em;
        $this->repo    = $repo;
        $this->scRepo  = $scRepo;
        $this->crsRepo = $crsRepo;
        $this->session = $session;
        $this->year = ($this->session->has('session_school_year') && ($this->session->get('session_school_year')!= null)) ? $this->session->get('session_school_year') : $this->scRepo->findOneBy(array("activated" => true));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $excludedIdsQb = $this->repo->createQueryBuilder('a')->andWhere('a.schoolYear=:year')->setParameter('year', $this->year->getId());
        $excludedIds = array();
        foreach($excludedIdsQb->getQuery()->getResult() as $result){
            $excludedIds[] =  $result->getCourse()->getId();
        }
        $builder
            ->add('course',  EntityType::class, array(
                'class' => Course::class,   'placeholder' => 'Choisir la matière', 'query_builder' => function () use ( $excludedIds) {
                    $qb = $this->crsRepo->createQueryBuilder('c');
                    if (!empty($excludedIds)) {
                        $qb->andWhere($qb->expr()->notIn('c.id', ':excludedIds'))
                        ->setParameter('excludedIds', $excludedIds);
                    }
                    return $qb ;
                }
            ))
            ->add('teacher', EntityType::class, array('class' => User::class,  'placeholder' => 'Choisir l\'enseignant ', 'label' => 'Enseignant', 'required' =>  true,  'query_builder' => function (UserRepository $repository) {
                return $repository->createQueryBuilder('u')->add('orderBy', 'u.fullName');
            }))
            ->add('headTeacher', CheckboxType::class, array('label' => 'Enseignant principal ?'))
            ; 
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Attribution::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'attribution';
    }
}

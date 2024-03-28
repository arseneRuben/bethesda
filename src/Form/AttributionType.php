<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Attribution;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AttributionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder/*,->add('course' EntityType::class, array(
            'class' => Course::class, 'placeholder' => 'Choisir la matière', 'required' => true, 'label' => 'Matière',
            'query_builder' => function (CourseRepository $repository) {
                return $repository->findNotAttributedCoursesAtActivatedYear();
            }
        ))
            OLD
             ->add('course',  EntityType::class, array( 'class' => Course::class,   'placeholder' => 'Choisir la matière','query_builder' => function (CourseRepository $repository)
        {return $repository->findNotAttributedCoursesAtActivatedYear();}
        
        ))
        ->add('course',  EntityType::class, array(
                'class' => Course::class,   'placeholder' => 'Choisir la matière', 'query_builder' => function (CourseRepository $repository) {
                    return $repository->createQueryBuilder('c')->where('c.attributed=:er')->setParameter('er', false)->add('orderBy', 'c.domain');
                }
        
        */

            ->add('course',  EntityType::class, array(
                'class' => Course::class,   'placeholder' => 'Choisir la matière', 'query_builder' => function (CourseRepository $repository) {
                    return $repository->findNotAttributedCoursesAtActivatedYear();
                }
            ))

            ->add('teacher', EntityType::class, array('class' => User::class,  'placeholder' => 'Choisir l\'enseignant ', 'label' => 'Enseignant', 'required' =>  true,  'query_builder' => function (UserRepository $repository) {
                return $repository->createQueryBuilder('u')->add('orderBy', 'u.fullName');
            }))
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

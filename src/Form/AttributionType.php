<?php

namespace AppBundle\Form\Type;
use AppBundle\Repository\CourseRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder  ->add('course','entity', array('class' => 'AppBundle\Entity\Course', 'placeholder' => 'Choisir la matière', 'required' => true,'label' => 'Matière',
        'query_builder' => function (CourseRepository $repository){return $repository->findNotAttributedCoursesAtActivatedYear();}
       /* OLD
                 'query_builder' => function (CourseRepository $repository)
            {return $repository->createQueryBuilder('c')->where('c.attributed=:er')->setParameter('er', false)->add('orderBy', 'c.domain');} 
        */
        ))
          
             ->add('teacher','entity', array('class' => 'AppBundle\Entity\User',  'placeholder' => 'Choisir l\'enseignant ','label' => 'Enseignant', 'required' =>  true,  'query_builder' => function (UserRepository $repository)
            {return $repository->createQueryBuilder('u')->add('orderBy', 'u.username');} ))
   
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attribution',
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

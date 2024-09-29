<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StudentType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('imageFile', VichImageType::class, [
                            'label' => 'Photo d identite(JPG or PNG)', 
                        'required' => false,
                        'allow_delete' => true,
                        'imagine_pattern' => 'student_filter_square_medium',
                        'download_uri' => false,
                        
                    ])
               /* ->add('matricule', TextType::class, [
                        'label' => 'Matricule',
                        'required' => true,
                        'constraints' => new Assert\NotBlank(),
                        'trim' => true])*/
                ->add('lastName', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,
                    'constraints' => new Assert\NotBlank(),
                    'trim' => true])
                ->add('firstName', TextType::class, [
                    'label' => 'Prénom',
                    'required' => false,
                    'trim' => true])
                ->add('gender', ChoiceType::class, array(
                    'constraints' => new Assert\NotBlank(),
                    'choices' => array(
                        'FEMME' => '1',
                        'HOMME' => '0',
                    ), 'label' => 'Sexe'))
                ->add('birthday', DateType::class, [
                    'label' => 'Date de naissance',
                    'widget' => 'single_text',
                    'required' => true,
                     'input' => 'datetime',
                    'html5' => true,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'yyyy-MM-dd',
                    'constraints' => new Assert\Date(),
                    'constraints' => new Assert\NotBlank(),
                    'trim' => true])
                
                ->add('birthplace',TextType::class, [
                    'label' => 'Lieu de naissance',
                    'required' => true,
                 
                    'trim' => true])
                ->add('residence',TextType::class, [
                    'label' => 'Résidence',
                    'required' => false,
                    'trim' => true])
                ->add('fatherName', TextType::class, [
                    'label' => 'Nom du Père',
                    'required' => true,
                    'constraints' => new Assert\NotBlank(),
                    'trim' => true])
                ->add('motherName', TextType::class, [
                    'label' => 'Nom de la Mère',
                    'required' => true,
                    'constraints' => new Assert\NotBlank(),
                    'trim' => true])
                ->add('primaryContact',TextType::class, [
                    'label' => 'Contact du père ou tuteur',
                    'required' => false,
                    'trim' => true])
                ->add('secondaryContact',TextType::class, [
                    'label' => 'Contact de la mère ou nourrice',
                    'required' => false,
                    'trim' => true])
                
                ->add('particularDisease', TextType::class, [
                    'label' => 'Maladie(s) particulière(s)',
                    'required' => false,
                    'trim' => true])
                ->add('otherInformations',TextType::class, [
                    'label' => 'Autres informations',
                    'required' => false,
                    'trim' => true])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Student::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'student';
    }

}

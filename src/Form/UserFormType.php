<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Security\Core\Security;


class UserFormType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $builder
            ->add('phoneNumber')
           

            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'required' => true,
                'constraints' => new Assert\NotBlank(),
                'trim' => true])
           
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                
                'html5' => true,
                'widget' => 'single_text',
                'constraints' => new Assert\Date(),
                'required' => true,
                'constraints' => new Assert\NotBlank(),
                'trim' => true])
            ->add('gender', ChoiceType::class, array(
                'constraints' => new Assert\NotBlank(),
                'choices' => array(
                    'FEMME' => 1,
                    'HOMME' => 0,
                ), 'label' => 'Sexe'))
            ->add('birthplace')
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'trim' => true])
            ->add('nationality', CountryType::class, [
                'label' => 'Nationalité',
                'required' => true,
                'constraints' => new Assert\NotBlank(),
                'trim' => true])
            ->add('location', TextType::class, [
                'label' => 'Résidence',
                'required' => true,
                'constraints' => new Assert\NotBlank(),
                'trim' => true])
            ->add('academicLevel', ChoiceType::class, array(
                'constraints' => new Assert\NotBlank(),
                'choices' => array(
                    'BAC' => 'BACCALAUREAT',
                    'LICENCE' => 'LICENCE',
                    'MASTER' => 'MASTER',
                    'IET' => 'IET',
                    'DOCTORAT' => 'DOCTORAT',
                ), 'label' => 'Niveau  académique'))
            ->add('numCni', TextType::class, [
                'label' => 'Numéro de CNI',
                'required' => true,
                'constraints' => new Assert\NotBlank(),
                'trim' => true])
           
            ->add('region', ChoiceType::class, array(
                'constraints' => new Assert\NotBlank(),
                'choices' => array(
                    'Adamaoua' => 'Adamaoua',
                    'Centre' => 'Centre',
                    'Est' => 'Est',
                    'Extrême-Nord' => 'Extrême-Nord',
                    'Littoral' => 'Littoral',
                    'Nord' => 'Nord',
                    'Nord-Ouest' => 'Nord-Ouest',
                    'Ouest' => 'Ouest',
                    'Sud' => 'Sud',
                    'Sud-Ouest' => 'Sud-Ouest',
                ), 'label' => 'Region d\'origine'))
                ->add('department', TextType::class, [
                    'label' => 'Departement d\'origine',
                    'required' => true,
                    'constraints' => new Assert\NotBlank(),
                    'trim' => true])
            
            ->add('domain')
            ->add('securityQuestion', ChoiceType::class, array(
                'constraints' => new Assert\NotBlank(),
                'choices' => array(
                    'Numero de CNI?' => 'cin_number',
                     'Quelle est la ville de naissance de maman?'=> 'mother_birthplace',
                   'Le metier dont tu reves d\'exercer depuis ton enfance?'=> 'favorite_job' ,
                    'Quel est ton quartier de residence?' =>'residence' ,
                ), 'label' => 'Question de securite'))
                ->add('securityAnswer', TextType::class, [
                    'label' => 'Réponse à la question de sécurité',
                    'required' => true,
                ]);

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    $builder
                    ->add('email')
                    ->add('status', ChoiceType::class, array(
                        'constraints' => new Assert\NotBlank(),
                        'choices' => array(
                            'ADMINISTRATEUR' => 'ADMIN',
                            'ELEVE' => 'ELEVE',
                            'ENSEIGNANT' => 'PROF',
                            'FINANCE' => 'FINANCE',
                            'PREFET d\'ETUDES' => 'PREFET',
                            'PRINCIPAL' => 'PRINCIPAL',
                        ), 'label' => 'Fonction'));
                }
                   
    
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

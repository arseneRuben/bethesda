<?php

// src/Form/ResetPasswordWithSecurityQuestionType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ResetPasswordWithSecurityQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('securityAnswer', TextType::class, [
                'label' => 'Your answer to the security question',
                'required' => true,
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'New password',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Reset Password',
            ]);
    }
}

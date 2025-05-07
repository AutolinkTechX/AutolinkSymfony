<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class)
            ->add('_password', PasswordType::class, [
                'label' => 'Password',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, // Enable CSRF protection
            'csrf_field_name' => '_csrf_token', // Ensure this matches the CSRF token ID in security.yaml
            'csrf_token_id' => 'authenticate', // Ensure this matches the CSRF token ID in security.yaml
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // Remove the form name prefix
    }
}
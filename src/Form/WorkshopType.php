<?php

namespace App\Form;

use App\Entity\Workshop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.'
                ]),
                new \Symfony\Component\Validator\Constraints\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.'
                ])
            ]
        ])
        ->add('description', TextType::class, [
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'La description ne peut pas être vide.'
                ]),
                new \Symfony\Component\Validator\Constraints\Length([
                    'min' => 10,
                    'minMessage' => 'La description doit comporter au moins {{ limit }} caractères.'
                ])
            ]
        ])
        ->add('DateDebut', DateTimeType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'La date de début ne peut pas être vide.'
                ])
            ]
        ])
        ->add('DateFin', DateTimeType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'La date de fin ne peut pas être vide.'
                ])
            ]
        ])
        ->add('imageFile', FileType::class, [
            'label' => 'Upload Photo',
            'mapped' => false,
            'required' => false,
        ])
        ->add('adresse', TextType::class, [
            'required' => false,
            'attr' => ['readonly' => true]
        ])
        ->add('availablePlaces', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
            'label' => 'Nombre de places disponibles',
            'required' => true,
            'attr' => ['min' => 0],
        ])
        ->add('price', \Symfony\Component\Form\Extension\Core\Type\MoneyType::class, [
            'label' => 'Prix',
            'required' => true,
            'currency' => 'TND',
        ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workshop::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'workshop',
        ]);
    }
}
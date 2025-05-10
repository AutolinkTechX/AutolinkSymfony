<?php

namespace App\Form;

use App\Entity\TaskWorkshop;
use App\Entity\Workshop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskWorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('DateDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('DateFin', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('workshop', EntityType::class, [
                'class' => Workshop::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskWorkshop::class,
        ]);
    }
}

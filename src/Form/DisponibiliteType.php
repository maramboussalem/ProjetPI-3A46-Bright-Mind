<?php

namespace App\Form;

use App\Entity\Disponibilite;
use App\Entity\ServiceMed;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHeure', null, [
                'widget' => 'single_text',
            ])
            ->add('estReserve')
            ->add('serviceMed', EntityType::class, [
                'class' => ServiceMed::class,
                'choice_label' => 'nomService',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Disponibilite::class,
        ]);
    }
}

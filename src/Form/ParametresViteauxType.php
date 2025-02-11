<?php

namespace App\Form;

use App\Entity\ParametresViteaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresViteauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('fc')
            ->add('fr')
            ->add('ecg')
            ->add('TAS')
            ->add('TAD')
            ->add('age')
            ->add('spo2')
            ->add('gsc')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParametresViteaux::class,
        ]);
    }
}

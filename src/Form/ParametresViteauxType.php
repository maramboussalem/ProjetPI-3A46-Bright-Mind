<?php

namespace App\Form;

use App\Entity\ParametresViteaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresViteauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du patient',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fc', IntegerType::class, [
                'label' => 'Fréquence cardiaque (FC)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fr', IntegerType::class, [
                'label' => 'Fréquence respiratoire (FR)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('ecg', TextType::class, [
                'label' => 'ECG',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tas', IntegerType::class, [
                'label' => 'Pression Artérielle Systolique (TAS)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tad', IntegerType::class, [
                'label' => 'Pression Artérielle Diastolique (TAD)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('spo2', IntegerType::class, [
                'label' => 'Saturation en oxygène (SpO2)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('gsc', IntegerType::class, [
                'label' => 'GSC',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => ParametresViteaux::class,
        'attr' => ['novalidate' => 'novalidate'],
        'csrf_protection' => true, // Ensure CSRF protection is enabled
    ]);
}

}

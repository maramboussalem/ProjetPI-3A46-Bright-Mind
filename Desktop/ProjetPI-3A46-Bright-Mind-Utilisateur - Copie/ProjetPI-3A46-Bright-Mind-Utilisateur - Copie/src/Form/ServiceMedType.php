<?php

namespace App\Form;

use App\Entity\ServiceMed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceMedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomService', TextType::class, [
                'label' => 'Nom du service médical',
                'attr' => ['placeholder' => 'Entrez le nom du service', 'maxlength' => 255],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du service est requis.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom du service ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('descriptionMed', TextareaType::class, [
                'label' => 'Description du service',
                'attr' => ['placeholder' => 'Fournissez une description détaillée', 'rows' => 5, 'maxlength' => 255],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description du service est requise.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceMed::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEquipement', TextType::class, [
                'label' => 'Nom de l\'équipement',
                'attr' => ['placeholder' => 'Entrez le nom de l\'équipement', 'maxlength' => 255],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de l\'équipement est requis.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Donnez une description détaillée', 'rows' => 4, 'maxlength' => 255],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est requise.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('quantiteStock', NumberType::class, [
                'label' => 'Quantité en stock',
                'attr' => ['placeholder' => 'Quantité en stock', 'min' => 0, 'step' => 'any'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité en stock est requise.']),
                    new Assert\PositiveOrZero(['message' => 'La quantité doit être un nombre positif ou égal à zéro.']),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'La quantité doit être supérieure ou égale à zéro.'
                    ])
                ]
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire',
                'attr' => ['placeholder' => 'Prix unitaire', 'min' => 0, 'step' => 'any'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix est requis.']),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le prix ne peut pas être négatif.'
                    ])
                ]
            ])
            ->add('dateAchat', DateType::class, [
                'label' => 'Date d\'achat',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'JJ/MM/AAAA'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date d\'achat est requise.']),
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date d\'achat ne peut pas être dans le futur.'
                    ])
                ]
            ])
            ->add('etatEquipement', ChoiceType::class, [
                'label' => 'État de l\'équipement',
                'choices' => [
                    'Neuf' => 'neuf',
                    'Bon état' => 'bon_etat',
                    'Usagé' => 'usage',
                    'Endommagé' => 'endommagé'
                ],
                'attr' => ['class' => 'custom-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'état de l\'équipement est requis.'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}

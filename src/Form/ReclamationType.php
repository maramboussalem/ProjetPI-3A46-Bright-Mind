<?php
// src/Form/ReclamationType.php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Consultation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length; 
use Symfony\Component\Validator\Constraints\NotNull; 

class ReclamationType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Add common fields.
        $builder
            ->add('utilisateurId', TextType::class, [
                'constraints' => [
                    // new NotBlank(['message' => 'Utilisateur ID is required.'])
                ],
            ])
            
            ->add('sujet', ChoiceType::class, [
                'choices' => [
                    'Problème avec la consultation en ligne' => 'Problème avec la consultation en ligne',
                    'Problème de communication avec le médecin' => 'Problème de communication avec le médecin',
                    'Erreur dans le diagnostic' => 'Erreur dans le diagnostic',
                    'Médicament non adapté' => 'Médicament non adapté',
                    'Autres' => 'Autres',
                ],
                'placeholder' => 'Choisir votre sujet', // Placeholder text when no choice is selected
                'constraints' => [
                    // new NotBlank(['message' => 'Sujet is required.'])
                ],
                'required' => false,  // Make this field optional initially
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    // new NotBlank(['message' => 'Description is required.']),
                    new Length(['max' => 1000, 'maxMessage' => 'Description cannot exceed 1000 characters.']),
                ],
            ]);

        // Always add the consultation field.
        // In "new2" mode, it will be disabled (shown but not editable).
        $consultationOptions = [
            'class' => Consultation::class,
            'choice_label' => function (Consultation $consultation) {
                return $consultation->getDateConsultation()->format('Y-m-d');
            },
            // 
        ];

        if ($options['is_new2']) {
            // Disable the field so the user cannot modify it.
            $consultationOptions['disabled'] = true;
        }

        $builder->add('consultation', EntityType::class, $consultationOptions);

        // Add admin-only field if applicable.
        if ($options['is_admin']) {
            $builder->add('reponse', TextareaType::class, [
                'required' => false,
                'label' => 'Réponse de l\'admin',
            ]);
        }

        // Only add the statut field when not in new2 mode.
        if (!$options['is_new2']) {
            $builder->add('statut', ChoiceType::class, [
                'choices' => [
                    'en cours' => 'en cours',
                    'terminé' => 'terminé',
                    'résolu' => 'résolu',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'is_admin' => false,
            'is_new2' => false,
        ]);
    }
}
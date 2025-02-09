<?php

// src/Form/UtilisateurType.php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                new Length(['min' => 2, 'minMessage' => 'Le nom doit comporter au moins 2 caractères.']),
                new Regex([
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'Le nom ne peut contenir que des lettres et des espaces.'
                ]),
            ],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'constraints' => [
                new NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                new Length(['min' => 2, 'minMessage' => 'Le prénom doit comporter au moins 2 caractères.']),
                new Regex([
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'Le prénom ne peut contenir que des lettres et des espaces.'
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(['message' => 'L\'email ne peut pas être vide.']),
                new Email(['message' => 'L\'email est invalide.']),
            ],
        ])
        ->add('motDePasse', PasswordType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Le mot de passe doit comporter au moins 8 caractères.',
                    'max' => 20,
                    'maxMessage' => 'Le mot de passe ne peut pas excéder 20 caractères.',
                ]),
                new Regex([
                    'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,20}$/',
                    'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.'
                ]),
                new Regex([
                    'pattern' => '/^\S*$/',
                    'message' => 'Le mot de passe ne peut pas contenir d\'espaces.'
                ]),
            ],
        ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                    new Length(['min' => 5, 'minMessage' => 'L\'adresse doit comporter au moins 5 caractères.']),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne peut pas être vide.']),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le numéro de téléphone doit être composé de 8 chiffres.',
                    ]),
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Patient' => 'patient',
                    'Médecin' => 'medecin',
                ],
                
                
            ])
            // Champs spécifiques au rôle Patient
            ->add('antecedentsMedicaux', TextareaType::class, [
                'label' => 'Antécédents médicaux',
                'required' => false,
                'attr' => ['class' => 'patient-fields'],
            ])
            // Champs spécifiques au rôle Médecin
            ->add('specialite', TextType::class, [
                'label' => 'Spécialité',
                'required' => false,
                'attr' => ['class' => 'doctor-fields'],
                
            ])
            ->add('hopital', TextType::class, [
                'label' => 'Hôpital',
                'required' => false,
                'attr' => ['class' => 'doctor-fields'],
            ])
            ->add('disponibilite', TextType::class, [
                'label' => 'Disponibilité',
                'required' => false,
                'attr' => ['class' => 'doctor-fields'],
            ])
            // Bouton de soumission
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}


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

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('motDePasse', PasswordType::class)
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone'
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


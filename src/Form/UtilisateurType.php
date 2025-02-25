<?php

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
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('email', EmailType::class)
        ->add('motdepasse', PasswordType::class)
        ->add('motdepasse_confirmation', PasswordType::class)
        ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            ->add('adresse', TextareaType::class)
            ->add('telephone', TextType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Patient' => 'patient',
                    'Médecin' => 'medecin',
                ],
            ])
            ->add('antecedentsMedicaux', TextareaType::class)
            ->add('specialite', TextType::class)
            ->add('hopital', TextType::class)
            ->add('disponibilite', TextType::class)
            ->add('file', FileType::class, [
                'label' => 'Upload File',
                'mapped' => false, // to prevent this field from being mapped to an entity
                'required' => false,
            ])
            ->add('captcha', TextType::class, [
                'label' => 'Entrez le code CAPTCHA',
            ])
        
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}


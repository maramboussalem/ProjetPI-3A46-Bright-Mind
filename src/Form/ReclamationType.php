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
        $consultations = $this->entityManager
            ->getRepository(Consultation::class)
            ->findAll();

        $consultationChoices = [];
        foreach ($consultations as $consultation) {
            $consultationChoices[$consultation->getNomPatient()] = $consultation->getId();
        }

        $builder
            ->add('utilisateurId', TextType::class, [
                'constraints' => [new NotBlank(['message' => 'Utilisateur ID is required.'])],
            ])
            ->add('sujet', ChoiceType::class, [
                'choices' => [
                    'Problème avec la consultation en ligne' => 'Problème avec la consultation en ligne',
                    'Problème de communication avec le médecin' => 'Problème de communication avec le médecin',
                    'Erreur dans le diagnostic' => 'Erreur dans le diagnostic',
                    'Médicament non adapté' => 'Médicament non adapté',
                    'Autres' => 'Autres',
                ],
                'constraints' => [new NotBlank(['message' => 'Sujet is required.'])],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Description is required.']),
                    new Length(['max' => 1000, 'maxMessage' => 'Description cannot exceed 1000 characters.']),
                ],
            ])
            ->add('consultation', EntityType::class, [
                'class' => Consultation::class,
                'choice_label' => function ($consultation) {
                    return $consultation->getDateConsultation()->format('Y-m-d');
                },
                'constraints' => [new NotNull(['message' => 'Consultation is required.'])],
            ]);

        if ($options['is_admin']) {
            $builder->add('reponse', TextareaType::class, [
                'required' => false,
                'label' => 'Réponse de l\'admin',
            ]);
        }

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





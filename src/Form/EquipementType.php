<?php

// src/Form/EquipementType.php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;


class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 500,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('nomEquipement', TextType::class, [
                'label' => 'Nom de l\'équipement',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de l\'équipement est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s-]+$/',
                        'message' => 'Le nom ne peut contenir que des lettres et des espaces.',
                    ]),
                ],
            ])
            ->add('quantiteStock', NumberType::class, [
                'label' => 'Quantité en stock',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\PositiveOrZero(['message' => 'La quantité doit être un nombre positif ou zéro.']),
                ],
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire (€)',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix unitaire est obligatoire.']),
                    new Assert\Positive(['message' => 'Le prix unitaire doit être un nombre positif.']),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Le prix doit être un nombre valide.',
                    ]),
                ],
            ])
            ->add('etatEquipement', ChoiceType::class, [
                'label' => 'État de l\'équipement',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Abimé' => 'Abimé',
                    'Réparé' => 'Réparé',
                ],
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez choisir un état pour l\'équipement.']),
                ],
            ])
            ->add('dateAchat', DateType::class, [
                'label' => 'Date d\'achat',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date d\'achat est obligatoire.']),
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date d\'achat ne peut pas être dans le futur.',
                    ]),
                ],
            ])
            ->add('img', FileType::class, [
                'label' => 'Image de l\'équipement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPG, PNG ou GIF.',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}

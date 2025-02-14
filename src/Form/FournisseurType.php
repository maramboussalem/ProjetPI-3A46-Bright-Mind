<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomFournisseur', TextType::class, [
                'label' => 'Nom du Fournisseur',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du fournisseur est obligatoire.']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez le nom du fournisseur'],
            ])
            ->add('Adresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez l\'adresse'],
            ])
            ->add('Telephone', TelType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => "/^\d{8}$/",
                        'message' => 'Le numéro de téléphone doit contenir exactement 8 chiffres.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez le numéro de téléphone'],
            ])
            ->add('Email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Assert\Email(['message' => 'Veuillez entrer une adresse email valide.']),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez l\'adresse email'],
            ]);
    }

    /**
     * Méthode de validation pour vérifier que les champs sont différents
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $data = $context->getObject();

        // Si le nom du fournisseur est identique à l'adresse
        if ($data->getNomFournisseur() === $data->getAdresse()) {
            $context->buildViolation('Le nom du fournisseur et l\'adresse ne peuvent pas être identiques.')
                ->atPath('Adresse')  // L'erreur sera liée au champ "Adresse"
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}
